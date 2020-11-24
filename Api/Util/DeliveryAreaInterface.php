<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Util;

/**
 * Utility for determining residences on islands which usually require special postal handling.
 *
 * @api
 */
interface DeliveryAreaInterface
{
    /**
     * Determine if given address is located on an island.
     *
     * @param string $countryCode
     * @param string $postalCode
     *
     * @return bool
     */
    public function isIsland(string $countryCode, string $postalCode): bool;
}
