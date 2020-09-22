<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;

/**
 * @api
 */
interface TrackResponseInterface
{
    public const TRACK_NUMBER = 'track_number';
    public const SALES_SHIPMENT = 'sales_shipment';
    public const SALES_TRACK = 'sales_track';

    /**
     * Obtain tracking number
     *
     * @return string
     */
    public function getTrackNumber(): string;

    /**
     * @return ShipmentInterface|null
     */
    public function getSalesShipment(): ?ShipmentInterface;

    /**
     * @return ShipmentTrackInterface|null
     */
    public function getSalesTrack(): ?ShipmentTrackInterface;
}
