<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\DeliveryLocation;

use Netresearch\ShippingCore\Api\Data\DeliveryLocation\AddressInterface;

/**
 * @api
 */
interface SearchInterface
{
    /**
     * @param string $carrierCode
     * @param \Netresearch\ShippingCore\Api\Data\DeliveryLocation\AddressInterface $searchAddress
     * @return \Netresearch\ShippingCore\Api\Data\DeliveryLocation\LocationInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function search(string $carrierCode, AddressInterface $searchAddress): array;
}
