<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Track\TrackRequest;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\TrackRequest\TrackRequestInterface;

class TrackRequest implements TrackRequestInterface
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @var string
     */
    private $trackNumber;

    /**
     * @var ShipmentInterface
     */
    private $salesShipment;

    /**
     * @var ShipmentTrackInterface
     */
    private $salesTrack;

    /**
     * TrackRequest constructor.
     *
     * @param int $storeId
     * @param string $trackNumber
     * @param ShipmentInterface|null $salesShipment
     * @param ShipmentTrackInterface|null $salesTrack
     */
    public function __construct(
        int $storeId,
        string $trackNumber,
        ?ShipmentInterface $salesShipment = null,
        ?ShipmentTrackInterface $salesTrack = null
    ) {
        $this->storeId = $storeId;
        $this->trackNumber = $trackNumber;
        $this->salesShipment = $salesShipment;
        $this->salesTrack = $salesTrack;
    }

    /**
     * Obtain store id
     *
     * @return int
     */
    #[\Override]
    public function getStoreId(): int
    {
        return $this->storeId;
    }

    /**
     * Obtain tracking number
     *
     * @return string
     */
    #[\Override]
    public function getTrackNumber(): string
    {
        return $this->trackNumber;
    }

    /**
     * @return ShipmentInterface|null
     */
    #[\Override]
    public function getSalesShipment(): ?ShipmentInterface
    {
        return $this->salesShipment;
    }

    /**
     * @return ShipmentTrackInterface|null
     */
    #[\Override]
    public function getSalesTrack(): ?ShipmentTrackInterface
    {
        return $this->salesTrack;
    }
}
