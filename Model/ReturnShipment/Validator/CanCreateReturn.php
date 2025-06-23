<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment\Validator;

use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\ShippingCore\Api\BulkShipment\ReturnShipmentConfigurationInterface;
use Netresearch\ShippingCore\Model\BulkShipment\ReturnShipmentConfiguration;

class CanCreateReturn
{
    /**
     * @var ReturnShipmentConfiguration
     */
    private $returnShipmentConfiguration;

    /**
     * @param ReturnShipmentConfiguration $returnShipmentConfiguration
     */
    public function __construct(ReturnShipmentConfiguration $returnShipmentConfiguration)
    {
        $this->returnShipmentConfiguration = $returnShipmentConfiguration;
    }

    /**
     * Check if a return shipment can be fulfilled for the given order by any or the specified carrier.
     *
     * @param OrderInterface $order
     * @param string|null $carrierCode
     * @return bool
     */
    public function execute(OrderInterface $order, ?string $carrierCode = null): bool
    {
        $hasShipments = ($order->getShipmentsCollection()->getSize() > 0);
        $carrierCodes = $carrierCode ? [$carrierCode] : $this->returnShipmentConfiguration->getCarrierCodes();
        $carrierConfigs = array_map(
            function (string $carrierCode) {
                return $this->returnShipmentConfiguration->getCarrierConfiguration($carrierCode);
            },
            $carrierCodes
        );

        $carrierConfigs = array_filter(
            $carrierConfigs,
            static function (ReturnShipmentConfigurationInterface $carrierConfig) use ($order) {
                return $carrierConfig->canProcessOrder($order);
            }
        );

        return $hasShipments && !empty($carrierConfigs);
    }
}
