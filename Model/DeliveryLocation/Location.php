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
    #[\Override]
    public function getShopType(): string
    {
        return $this->shopType;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getShopNumber(): string
    {
        return $this->shopNumber;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getShopId(): string
    {
        return $this->shopId;
    }

    /**
     * @return string[]
     */
    #[\Override]
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return AddressInterface
     */
    #[\Override]
    public function getAddress(): AddressInterface
    {
        return $this->address;
    }

    /**
     * @return OpeningHoursInterface[]
     */
    #[\Override]
    public function getOpeningHours(): array
    {
        return $this->openingHours;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return float
     */
    #[\Override]
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return float
     */
    #[\Override]
    public function getLatitude(): float
    {
        return $this->latitude;
    }
    /**
     * @return string
     */
    #[\Override]
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $shopType
     */
    #[\Override]
    public function setShopType(string $shopType): void
    {
        $this->shopType = $shopType;
    }

    /**
     * @param string $shopNumber
     */
    #[\Override]
    public function setShopNumber(string $shopNumber): void
    {
        $this->shopNumber = $shopNumber;
    }

    /**
     * @param string $shopId
     */
    #[\Override]
    public function setShopId(string $shopId): void
    {
        $this->shopId = $shopId;
    }

    /**
     * @param string[] $services
     */
    #[\Override]
    public function setServices(array $services): void
    {
        $this->services = $services;
    }

    /**
     * @param AddressInterface $address
     */
    #[\Override]
    public function setAddress(AddressInterface $address): void
    {
        $this->address = $address;
    }

    /**
     * @param OpeningHoursInterface[] $openingHours
     */
    #[\Override]
    public function setOpeningHours(array $openingHours): void
    {
        $this->openingHours = $openingHours;
    }

    /**
     * @param string $icon
     */
    #[\Override]
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @param float $latitude
     */
    #[\Override]
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @param float $longitude
     */
    #[\Override]
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @param string $displayName
     */
    #[\Override]
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }
}
