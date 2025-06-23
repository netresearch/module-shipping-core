<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment\Provider;

use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CurrentTrackInterface;

class CurrentTrack implements CurrentTrackInterface
{
    private $track = null;

    #[\Override]
    public function set(TrackInterface $track): void
    {
        $this->track = $track;
    }

    #[\Override]
    public function get(): ?TrackInterface
    {
        return $this->track;
    }
}
