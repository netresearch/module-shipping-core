<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;

/**
 * Instances of the metadata processor receive the shipping settings metadata
 * to modify them according to the current context.
 *
 * @api
 */
interface MetadataProcessorInterface
{
    /**
     * @param string $carrierCode
     * @param MetadataInterface $metadata
     * @param int $storeId
     * @param string $countryCode Recipient country
     * @param string $postalCode Recipient postal code
     * @param ShipmentInterface|null $shipment Shipment entity if available
     *
     * @return MetadataInterface Processed metadata
     * @throws \InvalidArgumentException
     */
    public function process(
        string $carrierCode,
        MetadataInterface $metadata,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ?ShipmentInterface $shipment = null
    ): MetadataInterface;
}
