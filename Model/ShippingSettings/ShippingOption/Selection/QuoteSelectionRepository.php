<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Netresearch\ShippingCore\Model\ResourceModel\Quote\Address\ShippingOptionSelection;
use Netresearch\ShippingCore\Model\ResourceModel\Quote\Address\ShippingOptionSelectionCollection;
use Netresearch\ShippingCore\Model\ResourceModel\Quote\Address\ShippingOptionSelectionCollectionFactory;

class QuoteSelectionRepository
{
    /**
     * @var ShippingOptionSelectionCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ShippingOptionSelection
     */
    private $resource;

    /**
     * QuoteServiceSelectionRepository constructor.
     *
     * @param ShippingOptionSelectionCollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ShippingOptionSelection $resource
     */
    public function __construct(
        ShippingOptionSelectionCollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ShippingOptionSelection $resource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->resource = $resource;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ShippingOptionSelectionCollection
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ShippingOptionSelectionCollection
    {
        $searchResult = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);

        return $searchResult;
    }

    /**
     * @param QuoteSelection $serviceSelection
     * @return QuoteSelection
     * @throws CouldNotSaveException
     */
    public function save(QuoteSelection $serviceSelection): QuoteSelection
    {
        try {
            $this->resource->save($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save service selection.'), $exception);
        }

        return $serviceSelection;
    }

    /**
     * @param QuoteSelection $serviceSelection
     * @throws CouldNotDeleteException
     */
    public function delete(QuoteSelection $serviceSelection): void
    {
        try {
            $this->resource->delete($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete service selection.'), $exception);
        }
    }
}
