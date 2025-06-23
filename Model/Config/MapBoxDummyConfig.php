<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Netresearch\ShippingCore\Api\Config\MapBoxConfigInterface;

/**
 * MapBox config proxy.
 *
 * Actual values are provided by some carrier module using the mapbox service.
 */
class MapBoxDummyConfig implements MapBoxConfigInterface
{
    #[\Override]
    public function getApiToken($store = null): string
    {
        return '';
    }

    #[\Override]
    public function getMapTileUrl($store = null): string
    {
        return '';
    }

    #[\Override]
    public function getMapAttribution($store = null): string
    {
        return '';
    }
}
