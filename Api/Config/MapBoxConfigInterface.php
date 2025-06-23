<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Config;

/**
 * @api
 */
interface MapBoxConfigInterface
{
    /**
     * @return string
     */
    public function getApiToken(mixed $store = null): string;

    /**
     * @return string
     */
    public function getMapTileUrl(mixed $store = null): string;

    /**
     * @return string
     */
    public function getMapAttribution(mixed $store = null): string;
}
