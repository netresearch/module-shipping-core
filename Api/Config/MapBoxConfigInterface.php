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
     * @param mixed $store
     * @return string
     */
    public function getApiToken($store = null): string;

    /**
     * @param mixed $store
     * @return string
     */
    public function getMapTileUrl($store = null): string;

    /**
     * @param mixed $store
     * @return string
     */
    public function getMapAttribution($store = null): string;
}
