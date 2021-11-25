<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\ShipmentFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\OrderDataProviderInterface;

class OrderDataProvider implements OrderDataProviderInterface
{
    /**
     * @var PackagingDataProvider
     */
    private $packageDataProvider;

    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    public function __construct(PackagingDataProvider $packageDataProvider, ShipmentFactory $shipmentFactory)
    {
        $this->packageDataProvider = $packageDataProvider;
        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * @param Order $order
     * @return CarrierDataInterface|null
     */
    public function getShippingOptions(Order $order): ?CarrierDataInterface
    {
        /** need to create a tmp shipment for packagingDataProvider */
        try {
            /** @var Order\Shipment $shipment */
            $shipment = $this->shipmentFactory->create($order);
            $packagingData = $this->packageDataProvider->getData($shipment);
        } catch (\RuntimeException $e) {
            return null;
        }
        $carrierCode = strtok((string) $order->getShippingMethod(), '_');
        $carrierData = $packagingData->getCarriers();

        $carrierData = array_filter(
            $carrierData,
            static function (CarrierDataInterface $carrierData) use ($carrierCode) {
                return $carrierData->getCode() === $carrierCode;
            }
        );

        return array_pop($carrierData);
    }
}
