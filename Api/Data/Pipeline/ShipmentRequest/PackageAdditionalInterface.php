<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest;

/**
 * @api
 */
interface PackageAdditionalInterface
{
    /**
     * Obtain additional (carrier-specific) package properties.
     *
     * @return mixed[]
     */
    public function getData(): array;
}
