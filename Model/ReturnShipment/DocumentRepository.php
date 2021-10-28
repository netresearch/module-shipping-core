<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentSearchResultsInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentSearchResultsInterfaceFactory;
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentRepositoryInterface;
use Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment\Document as DocumentResource;
use Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment\DocumentCollectionFactory;

class DocumentRepository implements DocumentRepositoryInterface
{
    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var DocumentResource
     */
    private $documentResource;

    /**
     * @var DocumentCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DocumentSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    public function __construct(
        DocumentFactory $documentFactory,
        DocumentResource $documentResource,
        DocumentCollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        DocumentSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->documentFactory = $documentFactory;
        $this->documentResource = $documentResource;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function get(int $documentId): DocumentInterface
    {
        $document = $this->documentFactory->create();

        try {
            $this->documentResource->load($document, $documentId);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Unable to load return shipment document with id %1.', $documentId));
        }

        if (!$document->getId()) {
            throw new NoSuchEntityException(__('Unable to load return shipment document with id %1.', $documentId));
        }

        return $document;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): DocumentSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @param DocumentInterface|Document $document
     * @return DocumentInterface|Document
     * @throws CouldNotSaveException
     */
    public function save(DocumentInterface $document): DocumentInterface
    {
        try {
            $this->documentResource->save($document);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save return shipment document.'), $exception);
        }

        return $document;
    }
}
