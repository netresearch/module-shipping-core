<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Rate\Emulation;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ProxyScopeConfig implements ScopeConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string[]
     */
    private $overrideMap;

    public function __construct(ScopeConfigInterface $scopeConfig, $overrideMap = [])
    {
        $this->scopeConfig = $scopeConfig;
        $this->overrideMap = $overrideMap;
    }

    public function getValue($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        if (array_key_exists($path, $this->overrideMap)) {
            return $this->overrideMap[$path];
        }

        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    public function isSetFlag($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        if (array_key_exists($path, $this->overrideMap)) {
            return $this->overrideMap[$path];
        }

        return $this->scopeConfig->isSetFlag($path, $scopeType, $scopeCode);
    }
}
