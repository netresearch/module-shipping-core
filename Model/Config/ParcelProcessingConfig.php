<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\Config\ParcelProcessingConfigInterface;
use Netresearch\ShippingCore\Model\ShippingBox\Package;
use Netresearch\ShippingCore\Model\ShippingBox\PackageFactory;

class ParcelProcessingConfig implements ParcelProcessingConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var PackageFactory
     */
    private $packageFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        PackageFactory $packageFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->packageFactory = $packageFactory;
    }

    public function getCodMethods($store = null): array
    {
        $paymentMethods = $this->scopeConfig->getValue(
            self::CONFIG_PATH_COD_METHODS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($paymentMethods)) {
            return [];
        }

        return explode(',', $paymentMethods);
    }

    public function isCodPaymentMethod(string $methodCode, $store = null): bool
    {
        return \in_array($methodCode, $this->getCodMethods($store), true);
    }

    public function getPackages($store = null): array
    {
        $packages = [];

        $packageParamsArray = $this->scopeConfig->getValue(
            self::CONFIG_PATH_PACKAGES,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        foreach ($packageParamsArray as $packageId => $packageParams) {
            $packages[] = $this->packageFactory->create([
                'id' => $packageId,
                'title' => $packageParams[self::CONFIG_FIELD_PACKAGE_TITLE],
                'width' => $packageParams[self::CONFIG_FIELD_PACKAGE_WIDTH],
                'length' => $packageParams[self::CONFIG_FIELD_PACKAGE_LENGTH],
                'height' => $packageParams[self::CONFIG_FIELD_PACKAGE_HEIGHT],
                'weight' => $packageParams[self::CONFIG_FIELD_PACKAGE_WEIGHT],
                'isDefault' => $packageParams[self::CONFIG_FIELD_PACKAGE_IS_DEFAULT] ?? false,
            ]);
        }

        return $packages;
    }

    public function getDefaultPackage($store = null): ?Package
    {
        foreach ($this->getPackages($store) as $package) {
            if ($package->isDefault()) {
                return $package;
            }
        }

        return null;
    }
}
