<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment\Track as TrackResource;

class TrackRepository implements TrackRepositoryInterface
{
    /**
     * @var TrackFactory
     */
    private $trackFactory;

    /**
     * @var TrackResource
     */
    private $trackResource;

    public function __construct(TrackFactory $trackFactory, TrackResource $trackResource)
    {
        $this->trackFactory = $trackFactory;
        $this->trackResource = $trackResource;
    }

    /**
     * @param int $trackId
     * @return TrackInterface
     * @throws NoSuchEntityException
     */
    public function get(int $trackId): TrackInterface
    {
        $track = $this->trackFactory->create();

        try {
            $this->trackResource->load($track, $trackId);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Unable to load return shipment with id %1.', $trackId));
        }

        if (!$track->getId()) {
            throw new NoSuchEntityException(__('Unable to load return shipment with id %1.', $trackId));
        }

        return $track;
    }

    /**
     * @param TrackInterface|Track $track
     * @return TrackInterface|Track
     * @throws CouldNotSaveException
     */
    public function save(TrackInterface $track): TrackInterface
    {
        try {
            $this->trackResource->save($track);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save return shipment.'), $exception);
        }

        return $track;
    }

    /**
     * @param TrackInterface|Track $track
     * @throws CouldNotDeleteException
     */
    public function delete(TrackInterface $track): void
    {
        try {
            $this->trackResource->delete($track);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Unable to delete return shipment.'), $exception);
        }
    }
}
