<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;

class OrderSelectionManager
{
    /**
     * @var OrderSelectionRepository
     */
    private $selectionRepository;

    /**
     * @var OrderSelectionFactory
     */
    private $selectionFactory;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    public function __construct(
        OrderSelectionRepository $selectionRepository,
        OrderSelectionFactory $selectionFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder
    ) {
        $this->selectionRepository = $selectionRepository;
        $this->selectionFactory = $selectionFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Load assigned service selection for given order shipping address ID.
     *
     * @param int $addressId
     *
     * @return SelectionInterface[]|OrderSelection[]
     */
    public function load(int $addressId): array
    {
        $addressFilter = $this->filterBuilder
            ->setField(AssignedSelectionInterface::PARENT_ID)
            ->setValue($addressId)
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();
        return $this->selectionRepository->getList($searchCriteria)->getItems();
    }

    /**
     * Assign service selection to given order shipping address ID and save to persistent storage.
     *
     * @param int $addressId
     * @param SelectionInterface[] $serviceSelection
     *
     * @return void
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function save(int $addressId, array $serviceSelection): void
    {
        foreach ($this->load($addressId) as $selection) {
            $this->selectionRepository->delete($selection);
        }

        foreach ($serviceSelection as $selection) {
            $data = [
                AssignedSelectionInterface::PARENT_ID => $addressId,
                AssignedSelectionInterface::SHIPPING_OPTION_CODE => $selection->getShippingOptionCode(),
                AssignedSelectionInterface::INPUT_CODE => $selection->getInputCode(),
                AssignedSelectionInterface::INPUT_VALUE => $selection->getInputValue(),
            ];

            $assignedSelection = $this->selectionFactory->create();
            $assignedSelection->setData($data);
            $this->selectionRepository->save($assignedSelection);
        }
    }

    /**
     * Set assigned service values to given shipping options.
     *
     * @param int $addressId
     * @param ShippingOptionInterface[] $shippingOptions
     *
     * @return void
     */
    public function apply(int $addressId, array $shippingOptions): void
    {
        // re-build service array, index by service option code
        $serviceSelection = array_reduce(
            $this->load($addressId),
            static function (array $carry, SelectionInterface $selection) {
                $carry[$selection->getShippingOptionCode()][$selection->getInputCode()] = $selection->getInputValue();
                return $carry;
            },
            []
        );

        foreach ($shippingOptions as $shippingOption) {
            $serviceValues = $serviceSelection[$shippingOption->getCode()] ?? [];
            if (empty($serviceValues)) {
                // no selection made for current shipping option, next…
                continue;
            }

            foreach ($shippingOption->getInputs() as $input) {
                if (isset($serviceValues[$input->getCode()])) {
                    $input->setDefaultValue($serviceValues[$input->getCode()]);
                }
            }
        }
    }
}
