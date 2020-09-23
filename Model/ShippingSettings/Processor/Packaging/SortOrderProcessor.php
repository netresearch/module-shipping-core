<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Processor\Packaging;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface;

class SortOrderProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * Sort shipping options and inputs according to their sort orders.
     *
     * @param ShippingOptionInterface[] $optionsData
     * @param ShipmentInterface $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, ShipmentInterface $shipment): array
    {
        uasort($optionsData, static function (ShippingOptionInterface $a, ShippingOptionInterface $b) {
            return $a->getSortOrder() - $b->getSortOrder();
        });

        foreach ($optionsData as $option) {
            $inputArray = $option->getInputs();

            uasort($inputArray, static function (InputInterface $a, InputInterface $b) {
                return $a->getSortOrder() - $b->getSortOrder();
            });

            $option->setInputs($inputArray);
        }

        return $optionsData;
    }
}
