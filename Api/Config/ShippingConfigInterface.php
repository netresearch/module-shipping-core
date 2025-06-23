<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Config;

use Magento\Framework\DataObject;

/**
 * Wrapper around Magento core modules' configuration settings.
 *
 * @api
 */
interface ShippingConfigInterface
{
    /**
     * Returns the shipping origin country code.
     *
     * @return string
     */
    public function getOriginCountry(mixed $store = null): string;

    /**
     * Returns the shipping origin region ID.
     *
     * @return int
     */
    public function getOriginRegion(mixed $store = null): int;

    /**
     * Returns the shipping origin city.
     *
     * @return string
     */
    public function getOriginCity(mixed $store = null): string;

    /**
     * Returns the shipping origin postal code.
     *
     * @return string|int
     */
    public function getOriginPostcode(mixed $store = null): string;

    /**
     * Returns the shipping origin street.
     *
     * @return string[]
     */
    public function getOriginStreet(mixed $store = null): array;

    /**
     * Returns all the store information settings wrapped in a data object.
     *
     * @return DataObject
     */
    public function getStoreInformation(mixed $store = null): DataObject;

    /**
     * Returns countries that are marked as EU-Countries
     *
     * @return string[]
     */
    public function getEuCountries(mixed $store = null): array;

    /**
     * Checks if route is dutiable by stores origin country and eu country list
     *
     * @param string $receiverCountry
     * @return bool
     *
     */
    public function isDutiableRoute(string $receiverCountry, mixed $store = null): bool;

    /**
     * Get the general weight unit.
     *
     * @return string - either kg or lb
     */
    public function getWeightUnit(mixed $store = null): string;

    /**
     * Get the normalized dimension unit
     *
     * @return string - either cm or in
     */
    public function getDimensionUnit(mixed $store = null): string;
}
