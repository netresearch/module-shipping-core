<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;

class ParcelProcessingConfig
{
    private const CONFIG_PATH_COD_METHODS  = 'shipping/parcel_processing/cod_methods';
    private const CONFIG_PATH_CUT_OFF_TIME = 'dhlshippingsolutions/dhlglobalwebservices/cut_off_time';
    private const CONFIG_PATH_PACKAGES = 'shipping/parcel_processing/packages';

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
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TimezoneInterface $timezone
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->timezone = $timezone;
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
     * Get the cut off time.
     *
     * @param mixed $store
     * @return \DateTime
     */
    public function getCutOffTime($store = null): \DateTime
    {
        $cutOffTimeRaw = (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_CUT_OFF_TIME,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        $cutOffTimeParts  = explode(',', $cutOffTimeRaw);

        list($hours, $minutes, $seconds) = array_map('intval', $cutOffTimeParts);

        return $this->timezone->scopeDate($store)->setTime($hours, $minutes, $seconds);
    }
}
