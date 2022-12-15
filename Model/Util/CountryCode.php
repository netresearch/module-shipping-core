<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

use Magento\Directory\Model\Country;
use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingCore\Api\Util\CountryCodeInterface;

/**
 * @deprecated
 * @see Alpha3Converter::convert
 */
class CountryCode implements CountryCodeInterface
{
    /**
     * @var Collection
     */
    private $countryCollection;

    public function __construct(CollectionFactory $countryCollectionFactory)
    {
        $this->countryCollection = $countryCollectionFactory->create();
    }

    public function getIso3Code(string $iso2Code): string
    {
        $country = $this->countryCollection->load()->getItemById($iso2Code);
        if (!$country instanceof Country) {
            throw new NoSuchEntityException(__('The country code %1 is not available.', $iso2Code));
        }

        return (string) $country->getData('iso3_code');
    }
}
