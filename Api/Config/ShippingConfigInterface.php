<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Config;

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
     * @param mixed $store
     * @return string
     */
    public function getOriginCountry($store = null): string;

    /**
     * Returns the shipping origin region ID.
     *
     * @param mixed $store
     * @return int
     */
    public function getOriginRegion($store = null): int;

    /**
     * Returns the shipping origin city.
     *
     * @param mixed $store
     * @return string
     */
    public function getOriginCity($store = null): string;

    /**
     * Returns the shipping origin postal code.
     *
     * @param mixed $store
     * @return string
     */
    public function getOriginPostcode($store = null): string;

    /**
     * Returns the shipping origin street.
     *
     * @param mixed $store
     * @return string[]
     */
    public function getOriginStreet($store = null): array;

    /**
     * Returns countries that are marked as EU-Countries
     *
     * @param mixed $store
     * @return string[]
     */
    public function getEuCountries($store = null): array;

    /**
     * Checks if route is dutiable by stores origin country and eu country list
     *
     * @param string $receiverCountry
     * @param mixed $store
     * @return bool
     *
     */
    public function isDutiableRoute(string $receiverCountry, $store = null): bool;

    /**
     * Get the general weight unit.
     *
     * @param mixed $store
     * @return string - either kg or lb
     */
    public function getWeightUnit($store = null): string;

    /**
     * Get the normalized dimension unit
     *
     * @param mixed $store
     * @return string - either cm or in
     */
    public function getDimensionUnit($store = null): string;
}
