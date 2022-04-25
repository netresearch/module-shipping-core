<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data;

/**
 * @api
 */
interface OrderItemAttributesInterface
{
    public const ITEM_ID = 'item_id';
    public const HS_CODE = 'hs_code';
    public const COUNTRY_OF_MANUFACTURE = 'country_of_manufacture';

    /**
     * @return int
     */
    public function getItemId(): int;

    /**
     * @return string
     */
    public function getHsCode(): string;

    /**
     * @return string
     */
    public function getCountryOfManufacture(): string;

    /**
     * @param int $itemId
     */
    public function setItemId(int $itemId): void;

    /**
     * @param string|null $hsCode
     */
    public function setHsCode(string $hsCode = null): void;

    /**
     * @param string|null $countryOfManufacture
     */
    public function setCountryOfManufacture(string $countryOfManufacture = null): void;
}
