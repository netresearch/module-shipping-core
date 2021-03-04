<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;

class SortOrderProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * Sort shipping options within one option group as well as their inputs according to their "sortOrder" property.
     *
     * @param string $carrierCode
     * @param ShippingOptionInterface[] $shippingOptions
     * @param int|null $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(
        string $carrierCode,
        array $shippingOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): array {
        uasort($shippingOptions, static function (ShippingOptionInterface $a, ShippingOptionInterface $b) {
            return $a->getSortOrder() - $b->getSortOrder();
        });

        foreach ($shippingOptions as $option) {
            $inputs = $option->getInputs();

            uasort($inputs, static function (InputInterface $a, InputInterface $b) {
                return $a->getSortOrder() - $b->getSortOrder();
            });

            $option->setInputs($inputs);
        }

        return $shippingOptions;
    }
}
