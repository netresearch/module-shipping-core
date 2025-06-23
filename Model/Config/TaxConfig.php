<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\AdditionalFee\TaxConfigInterface;

class TaxConfig implements TaxConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    #[\Override]
    public function getShippingTaxClass($scopeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_SHIPPING_TAX_CLASS,
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }

    #[\Override]
    public function isShippingPriceInclTax($scopeId = null): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_SHIPPING_INCLUDES_TAX,
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }

    #[\Override]
    public function displayCartPriceIncludingTax($scopeId = null): bool
    {
        return $this->getCartPriceDisplayType($scopeId) === self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    #[\Override]
    public function displayCartPriceExcludingTax($scopeId = null): bool
    {
        return $this->getCartPriceDisplayType($scopeId) === self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    #[\Override]
    public function displayCartBothPrices($scopeId = null): bool
    {
        return $this->getCartPriceDisplayType($scopeId) === self::DISPLAY_TYPE_BOTH;
    }

    #[\Override]
    public function getCartPriceDisplayType($scopeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_CART_SHIPPING,
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }

    #[\Override]
    public function displaySalesPriceIncludingTax($scopeId = null): bool
    {
        return $this->getSalesPriceDisplayType($scopeId) === self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    #[\Override]
    public function displaySalesPriceExcludingTax($scopeId = null): bool
    {
        return $this->getSalesPriceDisplayType($scopeId) === self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    #[\Override]
    public function displaySalesBothPrices($scopeId = null): bool
    {
        return $this->getSalesPriceDisplayType($scopeId) === self::DISPLAY_TYPE_BOTH;
    }

    #[\Override]
    public function getSalesPriceDisplayType($scopeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_SALES_SHIPPING,
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }
}
