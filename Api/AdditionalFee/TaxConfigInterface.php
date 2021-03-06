<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\AdditionalFee;

use Magento\Store\Model\Store;

/**
 * Interface TaxConfigInterface
 *
 * Wrapper around the Magento tax model, as it provides no public API
 *
 * @see \Magento\Tax\Model\Config
 *
 * @api
 */
interface TaxConfigInterface
{
    public const CONFIG_XML_PATH_SHIPPING_TAX_CLASS = 'tax/classes/shipping_tax_class';
    public const CONFIG_XML_PATH_SHIPPING_INCLUDES_TAX = 'tax/calculation/shipping_includes_tax';
    public const XML_PATH_DISPLAY_CART_SHIPPING = 'tax/cart_display/shipping';
    public const XML_PATH_DISPLAY_SALES_SHIPPING = 'tax/sales_display/shipping';

    /** Tax display types */
    public const DISPLAY_TYPE_EXCLUDING_TAX = 1;
    public const DISPLAY_TYPE_INCLUDING_TAX = 2;
    public const DISPLAY_TYPE_BOTH = 3;

    /**
     * Get tax class id specified for shipping tax estimation
     *
     * @param null|string|bool|int|Store $scopeId
     * @return mixed
     */
    public function getShippingTaxClass($scopeId = null): int;

    /**
     * If the shipping (and fee prices) in config are entered including taxes
     *
     * @param null|string|bool|int|Store $scopeId
     * @return bool
     */
    public function isShippingPriceInclTax($scopeId = null): bool;

    /**
     * Returns whether the shipping price should display with taxes included in cart view
     *
     * @param null|string|bool|int|Store $scopeId
     * @return bool
     */
    public function displayCartPriceIncludingTax($scopeId = null): bool;

    /**
     * Returns whether the shipping price should display with taxes excluded in cart view
     *
     * @param null|string|bool|int|Store $scopeId
     * @return bool
     */
    public function displayCartPriceExcludingTax($scopeId = null): bool;

    /**
     * Returns whether the shipping price should display with taxes included and excluded in cart view
     *
     * @param null|string|bool|int|Store $scopeId
     * @return bool
     */
    public function displayCartBothPrices($scopeId = null): bool;

    /**
     * Get shipping price display type in cart view
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param null|string|bool|int|Store $scopeId
     * @return int
     */
    public function getCartPriceDisplayType($scopeId = null): int;

    /**
     * Returns whether the shipping price should display with taxes included in sales view
     *
     * @param null|string|bool|int|Store $scopeId
     * @return bool
     */
    public function displaySalesPriceIncludingTax($scopeId = null): bool;

    /**
     * Returns whether the shipping price should display with taxes excluded in sales view
     *
     * @param null|string|bool|int|Store $scopeId
     * @return bool
     */
    public function displaySalesPriceExcludingTax($scopeId = null): bool;

    /**
     * Returns whether the shipping price should display with taxes included and excluded in sales view
     *
     * @param null|string|bool|int|Store $scopeId
     * @return bool
     */
    public function displaySalesBothPrices($scopeId = null): bool;

    /**
     * Get shipping price display type in sales view
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param null|string|bool|int|Store $scopeId
     * @return int
     */
    public function getSalesPriceDisplayType($scopeId = null): int;
}
