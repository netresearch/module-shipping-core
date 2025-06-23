<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Helper\Carrier;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Api\Util\UnitConverterInterface;

class ShippingConfig implements ShippingConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var UnitConverterInterface
     */
    private $unitConverter;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UnitConverterInterface $unitConverter,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->unitConverter = $unitConverter;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    #[\Override]
    public function getOriginCountry($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_COUNTRY_ID,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    #[\Override]
    public function getOriginRegion($store = null): int
    {
        return (int) $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_REGION_ID,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    #[\Override]
    public function getOriginCity($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_CITY,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    #[\Override]
    public function getOriginPostcode($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ZIP,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    #[\Override]
    public function getOriginStreet($store = null): array
    {
        $scope = ScopeInterface::SCOPE_STORE;

        return [
            (string) $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS1, $scope, $store),
            (string) $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS2, $scope, $store),
        ];
    }

    #[\Override]
    public function getStoreInformation($store = null): DataObject
    {
        $storeInfo = (array) $this->scopeConfig->getValue(
            'general/store_information',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->dataObjectFactory->create(['data' => $storeInfo]);
    }

    #[\Override]
    public function getEuCountries($store = null): array
    {
        $euCountries = $this->scopeConfig->getValue(
            Carrier::XML_PATH_EU_COUNTRIES_LIST,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return explode(',', (string) $euCountries);
    }

    #[\Override]
    public function isDutiableRoute(string $receiverCountry, $store = null): bool
    {
        $originCountry = $this->getOriginCountry($store);
        $euCountries = $this->getEuCountries($store);

        $bothEU = \in_array($originCountry, $euCountries, true) && \in_array($receiverCountry, $euCountries, true);

        return $receiverCountry !== $originCountry && !$bothEU;
    }

    #[\Override]
    public function getWeightUnit($store = null): string
    {
        $weightUOM = $this->scopeConfig->getValue(
            Data::XML_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->unitConverter->normalizeWeightUnit($weightUOM);
    }

    #[\Override]
    public function getDimensionUnit($store = null): string
    {
        $weightUOM = $this->getWeightUnit($store);

        return $weightUOM === 'kg' ? 'cm' : 'in';
    }
}
