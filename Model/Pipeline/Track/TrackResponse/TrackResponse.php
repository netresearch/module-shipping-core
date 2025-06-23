<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Track\TrackResponse;

use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;

class TrackResponse extends DataObject implements TrackResponseInterface
{
    /**
     * Obtain tracking number
     *
     * @return string
     */
    #[\Override]
    public function getTrackNumber(): string
    {
        return $this->getData(self::TRACK_NUMBER);
    }

    /**
     * @return ShipmentInterface|null
     */
    #[\Override]
    public function getSalesShipment(): ?ShipmentInterface
    {
        return $this->getData(self::SALES_SHIPMENT);
    }

    /**
     * @return ShipmentTrackInterface|null
     */
    #[\Override]
    public function getSalesTrack(): ?ShipmentTrackInterface
    {
        return $this->getData(self::SALES_TRACK);
    }
}
