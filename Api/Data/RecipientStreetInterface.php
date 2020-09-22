<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data;

/**
 * @api
 */
interface RecipientStreetInterface
{
    public const ORDER_ADDRESS_ID = 'order_address_id';
    public const NAME = 'name';
    public const NUMBER = 'number';
    public const SUPPLEMENT = 'supplement';

    /**
     * Get the order address id.
     *
     * @return int|null
     */
    public function getOrderAddressId(): ?int;

    /**
     * Get street name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get street number.
     *
     * @return string
     */
    public function getNumber(): string;

    /**
     * Get supplement.
     *
     * @return string
     */
    public function getSupplement(): string;
}
