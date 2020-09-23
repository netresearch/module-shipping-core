<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\Processor\Checkout;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;

/**
 * @api
 */
interface CompatibilityProcessorInterface
{
    /**
     * Receive an array of compatibility rule data items and modify them according to business logic.
     *
     * @param CompatibilityInterface[] $compatibilityData
     *
     * @return CompatibilityInterface[]
     */
    public function process(array $compatibilityData): array;
}
