<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Shipping;

/**
 * Utility for converting a shipping product code into its human-readable name.
 *
 * @api
 */
interface ProductNameProviderInterface
{
    /**
     * Obtain the code of the carrier that this name provider does support.
     *
     * @return string
     */
    public function getCarrierCode(): string;

    /**
     * Get the shipping product name identified by the given shipping product code.
     *
     * Returns empty string if code does not match any shipping product.
     *
     * @param string $productCode
     * @return string
     */
    public function getName(string $productCode): string;
}
