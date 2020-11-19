<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\AdditionalFee\Order;

use Magento\Sales\Api\Data\OrderExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Netresearch\ShippingCore\Model\AdditionalFee\TotalsManager;

/**
 * Add totals to order extension attributes.
 *
 * The additional totals columns in `sales_order` are necessary
 * for totals calculations but are not part of the `OrderInterface`.
 * In order to make the totals available for reading, this class shifts
 * them to extension attributes.
 */
class AddTotalsAsExtensionAttributes
{
    /**
     * @var OrderExtensionInterfaceFactory
     */
    private $orderExtensionFactory;

    public function __construct(OrderExtensionInterfaceFactory $orderExtensionFactory)
    {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * Shift totals columns to extension attributes when reading a single order.
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }
        $extensionAttributes->setBaseNrshippingAdditionalFee(
            $order->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME)
        );
        $extensionAttributes->setBaseNrshippingAdditionalFeeInclTax(
            $order->getData(TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME)
        );
        $extensionAttributes->setNrshippingAdditionalFee(
            $order->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
        );
        $extensionAttributes->setNrshippingAdditionalFeeInclTax(
            $order->getData(TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
        );

        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }

    /**
     * Shift totals columns to extension attributes when reading a list of orders.
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $searchResult
    ): OrderSearchResultInterface {
        foreach ($searchResult->getItems() as $order) {
            $this->afterGet($subject, $order);
        }

        return $searchResult;
    }
}
