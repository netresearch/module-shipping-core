<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ReturnShipment;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentSearchResultsInterface;

/**
 * @api
 */
interface DocumentRepositoryInterface
{
    /**
     * @param int $documentId
     * @return DocumentInterface
     * @throws NoSuchEntityException
     */
    public function get(int $documentId): DocumentInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return DocumentSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): DocumentSearchResultsInterface;

    /**
     * @param DocumentInterface $document
     * @return DocumentInterface
     * @throws CouldNotSaveException
     */
    public function save(DocumentInterface $document): DocumentInterface;
}
