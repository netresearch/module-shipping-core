<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\AdditionalFee\Tax;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\AbstractModel;
use Magento\Sales\Model\Order;
use Magento\Tax\Api\Data\OrderTaxDetailsItemInterface;
use Magento\Tax\Api\OrderTaxManagementInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use Netresearch\ShippingCore\Model\AdditionalFee\Total;
use Netresearch\ShippingCore\Model\AdditionalFee\TotalsManager;

/**
 * For invoice handling fix the tax amounts from the service fee.
 */
class AddFeeTaxAmounts
{
    /**
     * @var OrderTaxManagementInterface
     */
    private $orderTaxManagement;

    public function __construct(OrderTaxManagementInterface $orderTaxManagement)
    {
        $this->orderTaxManagement = $orderTaxManagement;
    }

    /**
     * @param TaxHelper $subject
     * @param array $result
     * @param Order|Order\Invoice|Order\Creditmemo $source
     * @return mixed[]
     *
     * @throws NoSuchEntityException
     */
    public function afterGetCalculatedTaxes(TaxHelper $subject, array $result, $source): array
    {
        if (!$source instanceof AbstractModel || $source instanceof Order) {
            // nothing to do for orders or arguments that are not sales documents
            return $result;
        }

        if ($source->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME) === null) {
            // no total in sales document, do nothing
            return $result;
        }

        $order = $source->getOrder();
        $orderTaxDetails = $this->orderTaxManagement->getOrderTaxDetails($order->getId());

        // Fetch original tax items
        /** @var OrderTaxDetailsItemInterface[] $items */
        $items = $orderTaxDetails->getItems() ?? [];
        $feeTax = array_filter(
            $items,
            function (OrderTaxDetailsItemInterface $item) {
                return $item->getType() === Total::NRSHIPPING_FEE_TAX_TYPE;
            }
        );
        $feeTax = array_shift($feeTax);
        if (!$feeTax) {
            // no fee tax amount registered, abort
            return $result;
        }

        /** @var OrderTaxDetailsItemInterface $feeTax */
        foreach ($feeTax->getAppliedTaxes() as $tax) {
            // update tax classes (full tax summary) with the amounts from the order
            foreach ($result as $index => $resultTax) {
                if ($resultTax['percent'] === (string) $tax->getPercent()) {
                    $resultTax['tax_amount'] += $tax->getAmount();
                    $resultTax['base_tax_amount'] += $tax->getBaseAmount();
                    $result[$index] = $resultTax;
                }
            }
        }

        return $result;
    }
}
