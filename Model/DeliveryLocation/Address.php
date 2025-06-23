<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\DeliveryLocation;

use Netresearch\ShippingCore\Api\Data\DeliveryLocation\AddressInterface;

class Address implements AddressInterface
{
    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $company;

    #[\Override]
    public function getStreet(): string
    {
        return $this->street;
    }

    #[\Override]
    public function getCity(): string
    {
        return $this->city;
    }

    #[\Override]
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    #[\Override]
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    #[\Override]
    public function getCompany(): string
    {
        return $this->company;
    }

    #[\Override]
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    #[\Override]
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    #[\Override]
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    #[\Override]
    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    #[\Override]
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }
}
