<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Config source model for the proxy carrier configuration field.
 */
class OfflineCarriers implements OptionSourceInterface
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
    public function toOptionArray(): array
    {
        $result = [];
        $carriers = $this->scopeConfig->getValue('carriers');
        if ($carriers) {
            $carriers = array_filter(
                $carriers,
                static function ($carrier) {
                    // Only use offline carriers
                    return !array_key_exists('is_online', $carrier) || (bool)$carrier['is_online'] === false;
                }
            );
            foreach (array_keys($carriers) as $carrierCode) {
                $result[] = [
                    'value' => $carrierCode,
                    'label' => ucfirst((string)$carrierCode),
                ];
            }
        }

        return $result;
    }
}
