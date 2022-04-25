<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\DeliveryLocation;

use Magento\Framework\Exception\LocalizedException;
use Netresearch\ShippingCore\Api\Data\DeliveryLocation\AddressInterface;
use Netresearch\ShippingCore\Api\Data\DeliveryLocation\LocationInterface;

/**
 * @api
 */
interface LocationProviderInterface
{
    /**
     * @param AddressInterface $address
     * @return LocationInterface[]
     * @throws LocalizedException
     */
    public function getLocationsByAddress(AddressInterface $address): array;

    /**
     * @return string
     */
    public function getCarrierCode(): string;
}
