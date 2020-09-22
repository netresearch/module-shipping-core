<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\Data;

use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\ShipperInterface;

class Shipper implements ShipperInterface
{
    /**
     * @var string
     */
    private $contactPersonName;

    /**
     * @var string
     */
    private $contactPersonFirstName;

    /**
     * @var string
     */
    private $contactPersonLastName;

    /**
     * @var string
     */
    private $contactCompanyName;

    /**
     * @var string
     */
    private $contactEmail;

    /**
     * @var string
     */
    private $contactPhoneNumber;

    /**
     * @var string[]
     */
    private $street;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $streetName;

    /**
     * @var string
     */
    private $streetNumber;

    /**
     * @var string
     */
    private $addressAddition;

    /**
     * Shipper constructor.
     * @param string $contactPersonName
     * @param string $contactPersonFirstName
     * @param string $contactPersonLastName
     * @param string $contactCompanyName
     * @param string $contactEmail
     * @param string $contactPhoneNumber
     * @param string[] $street
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string $countryCode
     * @param string $streetName
     * @param string $streetNumber
     * @param string $addressAddition
     */
    public function __construct(
        string $contactPersonName,
        string $contactPersonFirstName,
        string $contactPersonLastName,
        string $contactCompanyName,
        string $contactEmail,
        string $contactPhoneNumber,
        array $street,
        string $city,
        string $state,
        string $postalCode,
        string $countryCode,
        string $streetName,
        string $streetNumber,
        string $addressAddition
    ) {
        $this->contactPersonName = $contactPersonName;
        $this->contactPersonFirstName = $contactPersonFirstName;
        $this->contactPersonLastName = $contactPersonLastName;
        $this->contactCompanyName = $contactCompanyName;
        $this->contactEmail = $contactEmail;
        $this->contactPhoneNumber = $contactPhoneNumber;
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->postalCode = $postalCode;
        $this->countryCode = $countryCode;
        $this->streetName = $streetName;
        $this->streetNumber = $streetNumber;
        $this->addressAddition = $addressAddition;
    }

    public function getContactPersonName(): string
    {
        return $this->contactPersonName;
    }

    public function getContactPersonFirstName(): string
    {
        return $this->contactPersonFirstName;
    }

    public function getContactPersonLastName(): string
    {
        return $this->contactPersonLastName;
    }

    public function getContactCompanyName(): string
    {
        return $this->contactCompanyName;
    }

    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    public function getContactPhoneNumber(): string
    {
        return $this->contactPhoneNumber;
    }

    public function getStreet(): array
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getStreetName(): string
    {
        return $this->streetName;
    }

    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    public function getAddressAddition(): string
    {
        return $this->addressAddition;
    }
}
