<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ReturnShipment;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackSearchResultsInterface;

/**
 * @api
 */
interface TrackRepositoryInterface
{
    /**
     * @param int $trackId
     * @return TrackInterface
     * @throws NoSuchEntityException
     */
    public function get(int $trackId): TrackInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return TrackSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): TrackSearchResultsInterface;

    /**
     * @param TrackInterface $track
     * @return TrackInterface
     * @throws CouldNotSaveException
     */
    public function save(TrackInterface $track): TrackInterface;

    /**
     * @param TrackInterface $track
     * @throws CouldNotDeleteException
     */
    public function delete(TrackInterface $track): void;
}
