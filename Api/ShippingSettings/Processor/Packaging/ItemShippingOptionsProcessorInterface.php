<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\Processor\Packaging;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * @api
 */
interface ItemShippingOptionsProcessorInterface
{
    /**
     * Receive an array of shipping option items and modify them according to business logic.
     *
     * @param ItemShippingOptionsInterface[] $optionsData
     * @param ShipmentInterface $shipment
     *
     * @return ItemShippingOptionsInterface[]
     */
    public function process(array $optionsData, ShipmentInterface $shipment): array;
}
