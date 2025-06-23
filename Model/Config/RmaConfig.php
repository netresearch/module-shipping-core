<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\Config\RmaConfigInterface;

class RmaConfig implements RmaConfigInterface
{
    private const CONFIG_PATH_MAGENTO_RMA_ENABLED = 'sales/magento_rma/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    #[\Override]
    public function isRmaEnabledOnStoreFront($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_MAGENTO_RMA_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    #[\Override]
    public function getReturnAddress($store = null): array
    {
        $scope = ScopeInterface::SCOPE_STORE;

        $useStoreAddress = $this->scopeConfig->getValue('sales/magento_rma/use_store_address', $scope, $store);

        if ($useStoreAddress === null || $useStoreAddress === '1') {
            // flag not configured (CE) or explicitly set (EE).
            $address['city'] = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_CITY, $scope, $store);
            $address['country_id'] = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_COUNTRY_ID, $scope, $store);
            $address['postcode'] = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ZIP, $scope, $store);
            $address['region_id'] = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_REGION_ID, $scope, $store);
            $address['street2'] = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS2, $scope, $store);
            $address['street1'] = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS1, $scope, $store);
        } else {
            $address['city'] = $this->scopeConfig->getValue('sales/magento_rma/city', $scope, $store);
            $address['country_id'] = $this->scopeConfig->getValue('sales/magento_rma/country_id', $scope, $store);
            $address['postcode'] = $this->scopeConfig->getValue('sales/magento_rma/zip', $scope, $store);
            $address['region_id'] = $this->scopeConfig->getValue('sales/magento_rma/region_id', $scope, $store);
            $address['street2'] = $this->scopeConfig->getValue('sales/magento_rma/address1', $scope, $store);
            $address['street1'] = $this->scopeConfig->getValue('sales/magento_rma/address', $scope, $store);
        }

        return $address;
    }
}
