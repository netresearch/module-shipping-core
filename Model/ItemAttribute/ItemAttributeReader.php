<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ItemAttribute;

use Magento\Sales\Api\Data\OrderItemInterface;
use Netresearch\ShippingCore\Api\Util\ItemAttributeReaderInterface;

/**
 * Read product attributes from order items.
 */
class ItemAttributeReader implements ItemAttributeReaderInterface
{
    /**
     * Read HS code from extension attributes.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getHsCode(OrderItemInterface $orderItem): string
    {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            return '';
        }

        return (string) $extensionAttributes->getNrshippingTariffNumber();
    }

    /**
     * Read dangerous goods category from extension attributes.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getDgCategory(OrderItemInterface $orderItem): string
    {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            return '';
        }

        return (string)$extensionAttributes->getNrshippingDgCategory();
    }

    /**
     * Read export description from extension attributes.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getExportDescription(OrderItemInterface $orderItem): string
    {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            return '';
        }

        return (string) $extensionAttributes->getNrshippingExportDescription();
    }

    /**
     * Read country of manufacture from extension attributes.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getCountryOfManufacture(OrderItemInterface $orderItem): string
    {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            return '';
        }

        return (string) $extensionAttributes->getNrshippingCountryOfManufacture();
    }
}
