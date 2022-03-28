<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ReturnShipment;

use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;

/**
 * Utility for passing around a track entity.
 *
 * @api
 */
interface CurrentTrackInterface
{
    public function set(TrackInterface $track): void;

    public function get(): ?TrackInterface;
}
