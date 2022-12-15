<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingCore\Api\Util\CountryCodeConverterInterface;

/**
 * Convert ISO-3166-1 country codes from Alpha3 to Alpha2.
 */
class Alpha2Converter implements CountryCodeConverterInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var string[]
     */
    private $countryCodes;

    public function __construct(CollectionFactory $countryCollectionFactory)
    {
        $this->collectionFactory = $countryCollectionFactory;
    }

    public function convert(string $countryCode): string
    {
        if (empty($countryCode)) {
            return '';
        }

        if (empty($this->countryCodes)) {
            $collection = $this->collectionFactory->create();
            $collection->getSelect()->columns(['iso2_code', 'iso3_code']);
            $this->countryCodes = array_merge(
                array_column($collection->getData(), 'iso2_code', 'iso3_code'),
                array_column($collection->getData(), 'iso2_code', 'iso2_code')
            );
        }

        if (!isset($this->countryCodes[$countryCode])) {
            throw new NoSuchEntityException(__('The country code %1 is not available.', $countryCode));
        }

        return $this->countryCodes[$countryCode];
    }
}
