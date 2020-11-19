<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\AdditionalFee\Quote;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalSegmentExtensionFactory;
use Magento\Quote\Api\Data\TotalSegmentExtensionInterface;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Quote;
use Netresearch\ShippingCore\Model\AdditionalFee\Total;
use Netresearch\ShippingCore\Model\AdditionalFee\TotalsManager;

/**
 * Add additional data to the service charge total segment of the quote
 */
class AddTotalSegmentExtensionAttributes
{
    /**
     * @var TotalSegmentExtensionFactory
     */
    private $extensionAttributeFactory;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    public function __construct(
        TotalSegmentExtensionFactory $extensionAttributeFactory,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->extensionAttributeFactory = $extensionAttributeFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param CartTotalRepositoryInterface $subject
     * @param TotalsInterface $result
     * @param int $cartId
     * @return TotalsInterface
     * @throws NoSuchEntityException
     */
    public function afterGet(
        CartTotalRepositoryInterface $subject,
        TotalsInterface $result,
        int $cartId
    ): TotalsInterface {
        if (!array_key_exists(Total::SERVICE_CHARGE_TOTAL_CODE, $result->getTotalSegments())) {
            return $result;
        }

        $feeSegment = $result->getTotalSegments()[Total::SERVICE_CHARGE_TOTAL_CODE];
        $extensionAttributes = $feeSegment->getExtensionAttributes();
        if ($extensionAttributes === null) {
            /** @var TotalSegmentExtensionInterface $extensionAttributes */
            $extensionAttributes = $this->extensionAttributeFactory->create();
        }
        /** @var Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        $extensionAttributes->setNrshippingFee(
            (float) $quote->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
        );
        $extensionAttributes->setNrshippingFeeInclTax(
            (float) $quote->getData(TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
        );
        $feeSegment->setExtensionAttributes($extensionAttributes);
        $result->setTotalSegments(
            array_merge($result->getTotalSegments(), [Total::SERVICE_CHARGE_TOTAL_CODE => $feeSegment])
        );

        return $result;
    }
}
