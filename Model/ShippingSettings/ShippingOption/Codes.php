<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption;

/**
 * Carrier code, option code, and input code definitions for use in the shipping_settings.xml files.
 */
class Codes
{
    /**
     * The input type for the special shopfinder component.
     */
    public const INPUT_TYPE_SHOPFINDER = 'shopfinder';

    /**
     * The carrier code for the template carrier
     */
    public const CARRIER_BASE = 'base';

    public const PACKAGING_OPTION_DETAILS = 'packageDetails';
    public const PACKAGING_INPUT_PRODUCT_CODE = 'productCode';
    public const PACKAGING_INPUT_CUSTOM_PACKAGE_ID = 'customPackageId';
    public const PACKAGING_INPUT_PACKAGING_WEIGHT = 'packagingWeight';
    public const PACKAGING_INPUT_WEIGHT = 'weight';
    public const PACKAGING_INPUT_WEIGHT_UNIT = 'weightUnit';
    public const PACKAGING_INPUT_SIZE_UNIT = 'sizeUnit';
    public const PACKAGING_INPUT_WIDTH = 'width';
    public const PACKAGING_INPUT_HEIGHT = 'height';
    public const PACKAGING_INPUT_LENGTH = 'length';

    public const PACKAGING_OPTION_CUSTOMS = 'packageCustoms';
    public const PACKAGING_INPUT_CUSTOMS_VALUE = 'customsValue';
    public const PACKAGING_INPUT_EXPORT_DESCRIPTION = 'exportDescription';
    public const PACKAGING_INPUT_TERMS_OF_TRADE = 'termsOfTrade';
    public const PACKAGING_INPUT_CONTENT_TYPE = 'contentType';
    public const PACKAGING_INPUT_EXPLANATION = 'explanation';
    public const PACKAGING_INPUT_DG_CATEGORY = 'dgCategory';

    public const ITEM_OPTION_DETAILS = 'details';
    public const ITEM_INPUT_PRODUCT_ID = 'productId';
    public const ITEM_INPUT_PRODUCT_NAME = 'productName';
    public const ITEM_INPUT_PRICE = 'price';
    public const ITEM_INPUT_QTY = 'qty';
    public const ITEM_INPUT_QTY_TO_SHIP = 'qtyToShip';
    public const ITEM_INPUT_WEIGHT = 'weight';

    public const ITEM_OPTION_ITEM_CUSTOMS = 'itemCustoms';
    public const ITEM_INPUT_CUSTOMS_VALUE = 'customsValue';
    public const ITEM_INPUT_HS_CODE = 'hsCode';
    public const ITEM_INPUT_COUNTRY_OF_ORIGIN = 'countryOfOrigin';
    public const ITEM_INPUT_EXPORT_DESCRIPTION = 'exportDescription';

    public const SHOPFINDER_INPUT_COMPANY = 'company';
    public const SHOPFINDER_INPUT_LOCATION_TYPE = 'locationType';
    public const SHOPFINDER_INPUT_LOCATION_NUMBER = 'locationNumber';
    public const SHOPFINDER_INPUT_LOCATION_ID = 'locationId';
    public const SHOPFINDER_INPUT_STREET = 'street';
    public const SHOPFINDER_INPUT_POSTAL_CODE = 'postalCode';
    public const SHOPFINDER_INPUT_CITY = 'city';
    public const SHOPFINDER_INPUT_COUNTRY_CODE = 'countryCode';

    public const SERVICE_OPTION_CASH_ON_DELIVERY = 'cashOnDelivery';
    public const SERVICE_INPUT_COD_REASON_FOR_PAYMENT = 'reasonForPayment';
}
