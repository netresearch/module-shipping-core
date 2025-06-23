<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\DeliveryLocation;

use Magento\Framework\Exception\LocalizedException;
use Netresearch\ShippingCore\Api\Data\DeliveryLocation\AddressInterface;
use Netresearch\ShippingCore\Api\Data\DeliveryLocation\LocationInterface;
use Netresearch\ShippingCore\Api\DeliveryLocation\LocationProviderInterface;
use Netresearch\ShippingCore\Api\DeliveryLocation\SearchInterface;

class Search implements SearchInterface
{
    /**
     * @var LocationProviderInterface[]
     */
    private $locationProviders;

    /**
     * Search constructor.
     *
     * @param LocationProviderInterface[] $locationProviders
     */
    public function __construct($locationProviders = [])
    {
        $this->locationProviders = $locationProviders;
    }

    /**
     * @param string $carrierCode
     * @param AddressInterface $searchAddress
     * @return LocationInterface[]
     * @throws LocalizedException
     */
    #[\Override]
    public function search(string $carrierCode, AddressInterface $searchAddress): array
    {
        foreach ($this->locationProviders as $provider) {
            if ($provider->getCarrierCode() === $carrierCode) {
                return $provider->getLocationsByAddress($searchAddress);
            }
        }

        throw new \RuntimeException('No parcel shop location provider configured');
    }
}
