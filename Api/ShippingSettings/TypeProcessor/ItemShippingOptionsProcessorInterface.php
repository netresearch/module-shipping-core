<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Instances of the item shipping options processor receive the list of
 * item shipping options. These are a wrapper around the regular shipping
 * options type together with the current shipping item's id.
 *
 * Note that the actual item shipping options need to be created dynamically using
 * a data pre-processor. The item options defined in XML serve as a template only.
 * @see \Netresearch\ShippingCore\Model\ShippingSettings\ArrayProcessor\PrepareItemOptionsProcessor
 *
 * @api
 */
interface ItemShippingOptionsProcessorInterface
{
    /**
     * @param string $carrierCode
     * @param ItemShippingOptionsInterface[] $itemOptions
     * @param int $storeId
     * @param string $countryCode Recipient country
     * @param string $postalCode Recipient postal code
     * @param ShipmentInterface|null $shipment Shipment entity if available
     *
     * @return ItemShippingOptionsInterface[] Processed item options
     * @throws \InvalidArgumentException
     */
    public function process(
        string $carrierCode,
        array $itemOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ?ShipmentInterface $shipment = null
    ): array;
}
