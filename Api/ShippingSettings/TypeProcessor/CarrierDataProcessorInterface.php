<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;

/**
 * Instances of the carrier processor receive the entire carrier data to operate
 * on dependencies between sub-types, e.g. handle compatibilities between
 * package options and service options.
 *
 * @api
 */
interface CarrierDataProcessorInterface
{
    /**
     * @param CarrierDataInterface $shippingSettings
     * @param int $storeId
     * @param string $countryCode Recipient country
     * @param string $postalCode Recipient postal code
     * @param ShipmentInterface|null $shipment Shipment entity if available
     *
     * @return CarrierDataInterface Processed carrier settings
     * @throws \InvalidArgumentException
     */
    public function process(
        CarrierDataInterface $shippingSettings,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): CarrierDataInterface;
}
