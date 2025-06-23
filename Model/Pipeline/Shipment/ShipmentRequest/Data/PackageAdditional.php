<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\Data;

use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageAdditionalInterface;

class PackageAdditional implements PackageAdditionalInterface
{
    /**
     * Obtain additional (carrier-specific) package properties.
     *
     * @return mixed[]
     */
    #[\Override]
    public function getData(): array
    {
        return [];
    }
}
