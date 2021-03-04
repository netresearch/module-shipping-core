<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Instances of the compatibility processor receive the shipping settings
 * compatibility rules. This is mainly for the sake of completeness. There
 * has not been any use case yet for modifying compatibility rules.
 *
 * @api
 */
interface CompatibilityProcessorInterface
{
    /**
     * @param string $carrierCode
     * @param CompatibilityInterface[] $rules
     * @param int $storeId
     * @param string $countryCode Recipient country
     * @param string $postalCode Recipient postal code
     * @param ShipmentInterface|null $shipment Shipment entity if available
     *
     * @return CompatibilityInterface[] Processed compatibility rules
     * @throws \InvalidArgumentException
     */
    public function process(
        string $carrierCode,
        array $rules,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): array;
}
