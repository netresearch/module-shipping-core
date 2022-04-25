<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ItemAttribute;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item;
use Netresearch\ShippingCore\Api\Util\ItemAttributeReaderInterface;
use Netresearch\ShippingCore\Model\Util\ShipmentItemFilter;

/**
 * Read additional attributes from shipment items.
 */
class ShipmentItemAttributeReader
{
    /**
     * @var ItemAttributeReaderInterface
     */
    private $orderItemAttributeReader;

    /**
     * @var ShipmentItemFilter
     */
    private $itemFilter;

    public function __construct(
        ItemAttributeReaderInterface $orderItemAttributeReader,
        ShipmentItemFilter $itemFilter
    ) {
        $this->orderItemAttributeReader = $orderItemAttributeReader;
        $this->itemFilter = $itemFilter;
    }

    /**
     * Read country of manufacture from extension attributes.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getCountryOfManufacture(Item $shipmentItem): string
    {
        $orderItem = $shipmentItem->getOrderItem();
        return $this->orderItemAttributeReader->getCountryOfManufacture($orderItem);
    }

    /**
     * Read export description from extension attributes.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getExportDescription(Item $shipmentItem): string
    {
        $orderItem = $shipmentItem->getOrderItem();
        return $this->orderItemAttributeReader->getExportDescription($orderItem);
    }

    /**
     * Read HS code from extension attributes.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getHsCode(Item $shipmentItem): string
    {
        $orderItem = $shipmentItem->getOrderItem();
        return $this->orderItemAttributeReader->getHsCode($orderItem);
    }

    /**
     * Add together all items' weight multiplied by quantity to ship.
     *
     * @param ShipmentInterface|Shipment $shipment
     * @return float
     */
    public function getTotalWeight(ShipmentInterface $shipment): float
    {
        $fnAdd = static function ($totalWeight, Item $shipmentItem) {
            $totalWeight += $shipmentItem->getWeight() * $shipmentItem->getQty();
            return $totalWeight;
        };

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());
        return (float) array_reduce($items, $fnAdd, 0);
    }

    /**
     * Add together all items' price multiplied by quantity to ship.
     *
     * @param ShipmentInterface|Shipment $shipment
     * @return float
     */
    public function getTotalPrice(ShipmentInterface $shipment): float
    {
        $fnAdd = static function ($price, Item $shipmentItem) {
            $totalAmount = $shipmentItem->getOrderItem()->getBaseRowTotal()
                - $shipmentItem->getOrderItem()->getBaseDiscountAmount()
                + $shipmentItem->getOrderItem()->getBaseTaxAmount()
                + $shipmentItem->getOrderItem()->getBaseDiscountTaxCompensationAmount();

            $itemPrice = $totalAmount / $shipmentItem->getOrderItem()->getQtyOrdered();
            $price += ($itemPrice * $shipmentItem->getQty());

            return $price;
        };

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());
        return (float) array_reduce($items, $fnAdd, 0);
    }

    /**
     * Obtain all items' export description.
     *
     * @param ShipmentInterface|Shipment $shipment
     * @return string[]
     */
    public function getPackageExportDescriptions(ShipmentInterface $shipment): array
    {
        $fnCollect = function (Item $shipmentItem) {
            $itemExportDescription = $this->getExportDescription($shipmentItem);
            if ($itemExportDescription) {
                return $itemExportDescription;
            }

            if ($shipmentItem->getDescription()) {
                return $shipmentItem->getDescription();
            }

            return $shipmentItem->getName();
        };

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());
        return array_map($fnCollect, $items);
    }
}
