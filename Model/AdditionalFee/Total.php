<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\AdditionalFee;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Model\Order;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;
use Netresearch\ShippingCore\Api\AdditionalFee\TaxConfigInterface;
use Netresearch\ShippingCore\Api\Util\UnitConverterInterface;

class Total extends Address\Total\AbstractTotal
{
    public const SERVICE_CHARGE_TOTAL_CODE = 'nrshipping_additional_fee';
    public const NRSHIPPING_FEE_TAX_TYPE = 'nrshipping_fee';

    /**
     * @var string
     */
    protected $_code = self::SERVICE_CHARGE_TOTAL_CODE;

    /**
     * @var AdditionalFeeManagement
     */
    private $additionalFeeManagement;

    /**
     * @var UnitConverterInterface
     */
    private $unitConverter;

    /**
     * @var TotalsManager
     */
    private $totalsManager;

    /**
     * @var TaxHelper
     */
    private $taxHelper;

    /**
     * @var TaxConfigInterface
     */
    private $taxConfig;

    /**
     * @var TaxCalculationInterface
     */
    private $taxCalculation;

    public function __construct(
        AdditionalFeeManagement $additionalFeeManagement,
        UnitConverterInterface $unitConverter,
        TotalsManager $totalsManager,
        TaxHelper $taxHelper,
        TaxConfigInterface $taxConfig,
        TaxCalculationInterface $taxCalculation
    ) {
        $this->additionalFeeManagement = $additionalFeeManagement;
        $this->unitConverter = $unitConverter;
        $this->totalsManager = $totalsManager;
        $this->taxHelper = $taxHelper;
        $this->taxConfig = $taxConfig;
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * Retrieve label for the additional fee total, depending on the carrier.
     *
     * The original method signature does not contain arguments. We are
     * usually in control of calls to this method and take care of passing
     * in a valid shipping method. For the theoretical edge case where
     * Magento Core calls this method or something goes wrong, the
     * AdditionalFeeManagement determines a fallback label.
     *
     * @see AdditionalFeeManagement::getLabel
     *
     * @param string|null $shippingMethod
     * @return Phrase
     */
    public function getLabel(string $shippingMethod = null): Phrase
    {
        $carrierCode = strtok((string) $shippingMethod, '_');
        return $this->additionalFeeManagement->getLabel((string) $carrierCode);
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Address\Total $total
     * @return self
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Address\Total $total): self
    {
        parent::collect($quote, $shippingAssignment, $total);

        if (!$this->additionalFeeManagement->isActive($quote)) {
            // unset totals in case they were added to the quote before
            $this->totalsManager->unsetAdditionalFee($quote);
            return $this;
        }

        /** @var Address $shippingAddress */
        $shippingAddress = $shippingAssignment->getShipping()->getAddress();
        if ($shippingAddress->getAddressType() !== Address::ADDRESS_TYPE_SHIPPING) {
            // only collect total for shipping address to avoid glitches with gift-card-account module
            return $this;
        }

        $baseFee = $this->additionalFeeManagement->getTotalAmount($quote);
        if (abs($baseFee) > 0) {
            if ($shippingAddress->getBaseShippingAmount() + $baseFee < 0) {
                // make sure that shipping amount never drops below zero
                $baseFee = 0 - $shippingAddress->getBaseShippingAmount();
            }

            $taxClass = $this->taxHelper->getShippingTaxClass($quote->getStoreId());
            $taxRate = $this->taxCalculation->getCalculatedRate($taxClass);

            if ($this->taxConfig->isShippingPriceInclTax($quote->getStoreId())) {
                // price includes tax, deduct tax from total
                $baseFeeInclTax = $baseFee;
                $baseFee = $baseFee * 100 / ($taxRate + 100);
            } else {
                $baseFeeInclTax = $baseFee * ($taxRate + 100) / 100;
            }

            $total = $this->totalsManager->addFeeToTotal(
                $total,
                $baseFee,
                $baseFeeInclTax,
                $quote->getBaseCurrencyCode(),
                $quote->getQuoteCurrencyCode()
            );

            /**
             * add additional tax information to quote
             */
            $additionalFeeTaxInfo = [
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TYPE => self::NRSHIPPING_FEE_TAX_TYPE,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $total->getBaseTotalAmount(
                    self::SERVICE_CHARGE_TOTAL_CODE
                ),
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $total->getTotalAmount(
                    self::SERVICE_CHARGE_TOTAL_CODE
                ),
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_PRICE_INCLUDES_TAX => false,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_CODE => self::SERVICE_CHARGE_TOTAL_CODE,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_ASSOCIATION_ITEM_CODE => null,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_QUANTITY => 1,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $taxClass,
            ];

            /** @var string[][] $associates */
            $associates = $quote->getShippingAddress()->getAssociatedTaxables() ?? [];
            $associates[] = $additionalFeeTaxInfo;
            $quote->getShippingAddress()->setAssociatedTaxables($associates);

            $this->totalsManager->transferAdditionalFees($total, $quote);
        }

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Address\Total $total
     * @return mixed[]
     */
    public function fetch(Quote $quote, Address\Total $total): array
    {
        $result = [];
        $shippingAddress = $quote->getShippingAddress();

        if (!$shippingAddress->getId() || !$this->additionalFeeManagement->isActive($quote)) {
            return $result;
        }

        $baseFee = $this->additionalFeeManagement->getTotalAmount($quote);
        if ($shippingAddress->getBaseShippingAmount() + $baseFee < 0) {
            // make sure that shipping amount never drops below zero
            $baseFee = 0 - $shippingAddress->getBaseShippingAmount();
        }

        try {
            $fee = $this->unitConverter->convertMonetaryValue(
                $baseFee,
                $quote->getBaseCurrencyCode(),
                $quote->getQuoteCurrencyCode()
            );
        } catch (NoSuchEntityException $exception) {
            $fee = $baseFee;
        }

        if (abs($fee) > 0.0) {
            $result = [
                'code' => $this->getCode(),
                /**
                 * We need to use a Phrase object here, otherwise we get no title
                 *
                 * @see \Magento\Quote\Model\Cart\TotalsConverter::process
                 */
                'title' => $this->getLabel($shippingAddress->getShippingMethod()),
                'value' => $fee,
            ];
        }

        return $result;
    }

    /**
     * Generate an object that is used by the Magento core
     * to render the custom total.
     *
     * @param Order|Order\Invoice|Order\Creditmemo $source
     * @return DisplayObject|null
     */
    public function createTotalDisplayObject($source): ?DisplayObject
    {
        if ($source->getOrder()) {
            $shippingMethod = $source->getOrder()->getShippingMethod();
        } else {
            $shippingMethod = $source->getShippingMethod();
        }

        return $this->totalsManager->createTotalDisplayObject(
            $source,
            $this->getCode(),
            $this->getLabel($shippingMethod)->render()
        );
    }
}
