<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\SplitAddress;

use Magento\Sales\Api\Data\OrderAddressInterface;
use Netresearch\ShippingCore\Api\Data\RecipientStreetInterface;

/**
 * @api
 */
interface RecipientStreetLoaderInterface
{
    /**
     * Load or create a split address by given order address.
     *
     * @param OrderAddressInterface $address
     * @return RecipientStreetInterface
     */
    public function load(OrderAddressInterface $address): RecipientStreetInterface;
}
