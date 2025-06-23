<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\ArrayProcessor;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\ArrayProcessor\ShippingSettingsProcessorInterface;

class ShippingOptionAvailabilityProcessor implements ShippingSettingsProcessorInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Remove all shipping settings with an "available" config path property that evaluates to false.
     *
     * Per XSD, this only applies to shipping options (package and service options)
     * but processors do not know about the shipping settings schema.
     *
     * @param mixed[] $shippingSettings
     * @param int $storeId
     * @param ShipmentInterface|null $shipment
     *
     * @return mixed[]
     */
    #[\Override]
    public function process(array $shippingSettings, int $storeId, ?ShipmentInterface $shipment = null): array
    {
        foreach ($shippingSettings['carriers'] as $carrierCode => $carrierData) {
            foreach ($carrierData as $setting => $properties) {
                if (!is_array($properties)) {
                    continue;
                }

                foreach ($properties as $name => $values) {
                    $configPath = $values['available'] ?? '';
                    if (!$configPath) {
                        continue;
                    }

                    $isAvailable = $this->scopeConfig->isSetFlag($configPath, ScopeInterface::SCOPE_STORE, $storeId);

                    if (!$isAvailable) {
                        unset($shippingSettings['carriers'][$carrierCode][$setting][$name]);
                    } else {
                        unset($shippingSettings['carriers'][$carrierCode][$setting][$name]['available']);
                    }
                }
            }
        }

        return $shippingSettings;
    }
}
