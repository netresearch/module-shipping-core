<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\ArrayProcessor;

use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Instances of the settings processor receive all carriers' shipping settings
 * as plain array structure, parsed from the current scope's XML files.
 *
 * In context of label creation, the initial task of a data processor
 * would be to remove all shipping settings that do not apply to the
 * assigned carrier. In checkout context, all carriers' settings will
 * be retrieved via web api (local storage) and applied dynamically
 * based on the chosen shipping method.
 *
 * @api
 */
interface ShippingSettingsProcessorInterface
{
    /**
     * @param mixed[] $shippingSettings
     * @param int $storeId
     * @param ShipmentInterface|null $shipment Shipment entity if available
     *
     * @return mixed[] Processed shipping settings, indexed by carrier code.
     * @throws \InvalidArgumentException
     */
    public function process(array $shippingSettings, int $storeId, ShipmentInterface $shipment = null): array;
}
