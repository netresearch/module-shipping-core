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
interface SplittingRuleInterface
{
    /**
     * Apply custom rule after the street splitter regex did its work.
     *
     * @param OrderAddressInterface $address
     * @param RecipientStreetInterface $recipientStreet
     */
    public function apply(OrderAddressInterface $address, RecipientStreetInterface $recipientStreet): void;
}
