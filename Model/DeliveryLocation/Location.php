<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\DeliveryLocation;

use Netresearch\ShippingCore\Api\Data\DeliveryLocation\AddressInterface;
use Netresearch\ShippingCore\Api\Data\DeliveryLocation\LocationInterface;
use Netresearch\ShippingCore\Api\Data\DeliveryLocation\OpeningHoursInterface;

class Location implements LocationInterface
{
    /**
     * @var string
     */
    private $shopType;

    /**
     * @var string
     */
    private $shopNumber;

    /**
     * @var string
     */
    private $shopId;

    /**
     * @var string[]
     */
    private $services;

    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * @var OpeningHoursInterface[]
     */
    private $openingHours;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @return string
     */
    public function getShopType(): string
    {
        return $this->shopType;
    }

    /**
     * @return string
     */
    public function getShopNumber(): string
    {
        return $this->shopNumber;
    }

    /**
     * @return string
     */
    public function getShopId(): string
    {
        return $this->shopId;
    }

    /**
     * @return string[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return AddressInterface
     */
    public function getAddress(): AddressInterface
    {
        return $this->address;
    }

    /**
     * @return OpeningHoursInterface[]
     */
    public function getOpeningHours(): array
    {
        return $this->openingHours;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }
    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $shopType
     */
    public function setShopType(string $shopType): void
    {
        $this->shopType = $shopType;
    }

    /**
     * @param string $shopNumber
     */
    public function setShopNumber(string $shopNumber): void
    {
        $this->shopNumber = $shopNumber;
    }

    /**
     * @param string $shopId
     */
    public function setShopId(string $shopId): void
    {
        $this->shopId = $shopId;
    }

    /**
     * @param string[] $services
     */
    public function setServices(array $services): void
    {
        $this->services = $services;
    }

    /**
     * @param AddressInterface $address
     */
    public function setAddress(AddressInterface $address): void
    {
        $this->address = $address;
    }

    /**
     * @param OpeningHoursInterface[] $openingHours
     */
    public function setOpeningHours(array $openingHours): void
    {
        $this->openingHours = $openingHours;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }
}
