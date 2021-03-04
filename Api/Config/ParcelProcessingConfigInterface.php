<?php

namespace Netresearch\ShippingCore\Api\Config;

use Netresearch\ShippingCore\Model\ShippingBox\Package;

/**
 * @api
 */
interface ParcelProcessingConfigInterface
{
    public const CONFIG_PATH_COD_METHODS  = 'shipping/parcel_processing/cod_methods';
    public const CONFIG_PATH_COD_REASON_FOR_PAYMENT = 'shipping/parcel_processing/cod_reason_for_payment';
    public const CONFIG_PATH_PACKAGES = 'shipping/parcel_processing/packages';
    public const CONFIG_PATH_CONTENT_TYPE = 'shipping/parcel_processing/export_content_type';
    public const CONFIG_PATH_CONTENT_EXPLANATION = 'shipping/parcel_processing/export_content_explanation';

    public const CONFIG_FIELD_PACKAGE_ID = 'id';
    public const CONFIG_FIELD_PACKAGE_TITLE = 'title';
    public const CONFIG_FIELD_PACKAGE_WIDTH = 'width';
    public const CONFIG_FIELD_PACKAGE_LENGTH = 'length';
    public const CONFIG_FIELD_PACKAGE_HEIGHT = 'height';
    public const CONFIG_FIELD_PACKAGE_WEIGHT = 'weight';
    public const CONFIG_FIELD_PACKAGE_IS_DEFAULT = 'is_default';

    /**
     * Get payment methods that were marked as cash on delivery methods in configuration
     *
     * @param mixed $store
     * @return string[]
     */
    public function getCodMethods($store = null): array;

    /**
     * Check whether a payment method code was marked as cash on delivery method
     *
     * @param string $methodCode
     * @param mixed $store
     * @return bool
     */
    public function isCodPaymentMethod(string $methodCode, $store = null): bool;

    /**
     * Obtain all configured packages.
     *
     * @param mixed $store
     * @return Package[]
     */
    public function getPackages($store = null): array;

    /**
     * Obtain the package configured as default
     *
     * @param mixed $store
     * @return Package|null
     */
    public function getDefaultPackage($store = null): ?Package;
}
