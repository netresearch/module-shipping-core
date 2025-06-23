<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;

/**
 * Instances of the data processor receive all carriers' deserialized shipping data.
 *
 * @api
 */
interface ShippingDataProcessorInterface
{
    /**
     * @param ShippingDataInterface $shippingData
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment Shipment entity if available
     *
     * @return ShippingDataInterface Processed shipping data.
     * @throws \InvalidArgumentException
     */
    public function process(
        ShippingDataInterface $shippingData,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ?ShipmentInterface $shipment = null
    ): ShippingDataInterface;
}
