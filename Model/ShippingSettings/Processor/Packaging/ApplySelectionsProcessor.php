<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Processor\Packaging;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\OrderSelectionRepository;

class ApplySelectionsProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var OrderSelectionRepository
     */
    private $selectionRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        OrderSelectionRepository $selectionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->selectionRepository = $selectionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param ShipmentInterface $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, ShipmentInterface $shipment): array
    {
        $addressId  = (int) $shipment->getShippingAddressId();
        $selections = $this->loadSelections($addressId);

        foreach ($selections as $selection) {
            foreach ($optionsData as $shippingOption) {
                if ($shippingOption->getCode() !== $selection->getShippingOptionCode()) {
                    continue;
                }

                foreach ($shippingOption->getInputs() as $input) {
                    if ($input->getCode() !== $selection->getInputCode()) {
                        continue;
                    }

                    $input->setDefaultValue($selection->getInputValue());
                }
            }
        }

        return $optionsData;
    }

    /**
     * @param int $orderAddressId
     * @return AssignedSelectionInterface[]
     */
    private function loadSelections(int $orderAddressId): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                AssignedSelectionInterface::PARENT_ID,
                $orderAddressId
            )->create();

        return $this->selectionRepository->getList($searchCriteria)->getItems();
    }
}
