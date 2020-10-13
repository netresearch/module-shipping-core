<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Model\ShippingBox\Package;
use Netresearch\ShippingCore\Model\ShippingBox\PackageFactory;

class ParcelProcessingConfig
{
    private const CONFIG_PATH_COD_METHODS  = 'shipping/parcel_processing/cod_methods';
    private const CONFIG_PATH_PACKAGES = 'shipping/parcel_processing/packages';
    private const CONFIG_PATH_COD_REASON_FOR_PAYMENT = 'shipping/parcel_processing/cod_reason_for_payment';

    public const CONFIG_FIELD_PACKAGE_ID = 'id';
    public const CONFIG_FIELD_PACKAGE_TITLE = 'title';
    public const CONFIG_FIELD_PACKAGE_WIDTH = 'width';
    public const CONFIG_FIELD_PACKAGE_LENGTH = 'length';
    public const CONFIG_FIELD_PACKAGE_HEIGHT = 'height';
    public const CONFIG_FIELD_PACKAGE_WEIGHT = 'weight';
    public const CONFIG_FIELD_PACKAGE_IS_DEFAULT = 'is_default';

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

    /**
     * Get payment methods that were marked as cash on delivery methods in configuration
     *
     * @param mixed $store
     * @return string[]
     */
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

    /**
     * Check whether a payment method code was marked as cash on delivery method
     *
     * @param string $methodCode
     * @param mixed $store
     * @return bool
     */
    public function isCodPaymentMethod(string $methodCode, $store = null): bool
    {
        return \in_array($methodCode, $this->getCodMethods($store), true);
    }

    /**
     * Obtain all configured packages.
     *
     * @param mixed $store
     * @return Package[]
     */
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

    /**
     * Obtain the package configured as default
     *
     * @param mixed $store
     * @return Package|null
     */
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
