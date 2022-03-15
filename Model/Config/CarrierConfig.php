<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\Config\CarrierConfigInterface;

class CarrierConfig implements CarrierConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isActive(string $carrierCode, $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'carriers/' . $carrierCode . '/active',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getTitle(string $carrierCode, $store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            'carriers/' . $carrierCode . '/title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
