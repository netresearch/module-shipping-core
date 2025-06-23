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
    public const CONFIG_PATH_LABEL_EMAIL_ENABLED = 'shipping/parcel_processing/label_email_enabled';
    public const CONFIG_PATH_LABEL_EMAIL_ADDRESS = 'shipping/parcel_processing/label_email_address';

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
     * @return string[]
     */
    public function getCodMethods(mixed $store = null): array;

    /**
     * Check whether a payment method code was marked as cash on delivery method
     *
     * @param string $methodCode
     * @return bool
     */
    public function isCodPaymentMethod(string $methodCode, mixed $store = null): bool;

    /**
     * Obtain all configured packages.
     *
     * @return Package[]
     */
    public function getPackages(mixed $store = null): array;

    /**
     * Obtain the package configured as default
     *
     * @return Package|null
     */
    public function getDefaultPackage(mixed $store = null): ?Package;

    /**
     * Check if created shipping labels should be sent via email.
     *
     * @return bool
     */
    public function isShippingLabelEmailEnabled(mixed $store = null): bool;

    /**
     * Obtain the email address that new shipping labels should be sent to.
     *
     * @return string
     */
    public function getShippingLabelEmailAddress(mixed $store = null): string;
}
