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
 * @api
 */
class MapBoxConfig
{
    /**
     * @var MapBoxConfigInterface|null
     */
    private $carrierConfig;

    public function __construct(MapBoxConfigInterface $carrierConfig)
    {
        $this->carrierConfig = $carrierConfig;
    }

    public function getApiToken($store = null): string
    {
        return $this->carrierConfig->getApiToken($store);
    }

    public function getMapTileUrl($store = null): string
    {
        return $this->carrierConfig->getMapTileUrl($store);
    }

    public function getMapAttribution($store = null): string
    {
        return $this->carrierConfig->getMapAttribution($store);
    }
}
