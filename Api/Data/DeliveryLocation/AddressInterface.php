<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\DeliveryLocation;

/**
 * @api
 */
interface AddressInterface
{
    /**
     * @return string
     */
    public function getStreet(): string;

    /**
     * @return string
     */
    public function getCity(): string;

    /**
     * @return string
     */
    public function getCountryCode(): string;

    /**
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * @return string
     */
    public function getCompany(): string;

    /**
     * @param string $street
     * @return void
     */
    public function setStreet(string $street): void;

    /**
     * @param string $city
     * @return void
     */
    public function setCity(string $city): void;

    /**
     * @param string $countryCode
     * @return void
     */
    public function setCountryCode(string $countryCode): void;

    /**
     * @param string $postalCode
     * @return void
     */
    public function setPostalCode(string $postalCode): void;

    /**
     * @param string $company
     * @return void
     */
    public function setCompany(string $company): void;
}
