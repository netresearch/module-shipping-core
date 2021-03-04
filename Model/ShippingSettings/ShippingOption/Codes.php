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
     * The carrier code for the template carrier
     */
    public const CARRIER_BASE = 'base';

    // item details
    public const ITEM_OPTION_DETAILS = 'itemDetails';
    public const ITEM_INPUT_PRODUCT_ID = 'productId';
    public const ITEM_INPUT_PRODUCT_NAME = 'productName';
    public const ITEM_INPUT_PRICE = 'price';
    public const ITEM_INPUT_QTY = 'qty';
    public const ITEM_INPUT_QTY_TO_SHIP = 'qtyToShip';
    public const ITEM_INPUT_WEIGHT = 'weight';

    // item customs
    public const ITEM_OPTION_CUSTOMS = 'itemCustoms';
    public const ITEM_INPUT_CUSTOMS_VALUE = 'customsValue';
    public const ITEM_INPUT_COUNTRY_OF_ORIGIN = 'countryOfOrigin';
    public const ITEM_INPUT_HS_CODE = 'hsCode';
    public const ITEM_INPUT_EXPORT_DESCRIPTION = 'exportDescription';

    // package details
    public const PACKAGE_OPTION_DETAILS = 'packageDetails';
    public const PACKAGE_INPUT_PRODUCT_CODE = 'productCode';
    public const PACKAGE_INPUT_PACKAGING_ID = 'packagingId';
    public const PACKAGE_INPUT_PACKAGING_WEIGHT = 'packagingWeight';
    public const PACKAGE_INPUT_WEIGHT_UNIT = 'weightUnit';
    public const PACKAGE_INPUT_WEIGHT = 'weight';
    public const PACKAGE_INPUT_SIZE_UNIT = 'sizeUnit';
    public const PACKAGE_INPUT_LENGTH = 'length';
    public const PACKAGE_INPUT_WIDTH = 'width';
    public const PACKAGE_INPUT_HEIGHT = 'height';

    // package customs
    public const PACKAGE_OPTION_CUSTOMS = 'packageCustoms';
    public const PACKAGE_INPUT_CUSTOMS_VALUE = 'customsValue';
    public const PACKAGE_INPUT_CONTENT_TYPE = 'contentType';
    public const PACKAGE_INPUT_EXPLANATION = 'explanation';
    public const PACKAGE_INPUT_EXPORT_DESCRIPTION = 'exportDescription';

    // cash on delivery service
    public const SERVICE_OPTION_CASH_ON_DELIVERY = 'cashOnDelivery';
    public const SERVICE_INPUT_COD_REASON_FOR_PAYMENT = 'reasonForPayment';

    // delivery location service
    public const SERVICE_OPTION_DELIVERY_LOCATION = 'deliveryLocation';
    public const INPUT_TYPE_LOCATION_FINDER = 'locationfinder';
    public const SERVICE_INPUT_DELIVERY_LOCATION_TYPE = 'type';
    public const SERVICE_INPUT_DELIVERY_LOCATION_ID = 'id';
    public const SERVICE_INPUT_DELIVERY_LOCATION_NUMBER = 'number';
    public const SERVICE_INPUT_DELIVERY_LOCATION_COMPANY = 'company';
    public const SERVICE_INPUT_DELIVERY_LOCATION_COUNTRY_CODE = 'countryCode';
    public const SERVICE_INPUT_DELIVERY_LOCATION_POSTAL_CODE = 'postalCode';
    public const SERVICE_INPUT_DELIVERY_LOCATION_CITY = 'city';
    public const SERVICE_INPUT_DELIVERY_LOCATION_STREET = 'street';
}
