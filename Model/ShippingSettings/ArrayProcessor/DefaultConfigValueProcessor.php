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

class DefaultConfigValueProcessor implements ShippingSettingsProcessorInterface
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
     * Convert all inputs' "defaultConfigValue" property to an evaluated "defaultValue" property.
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
        foreach ($shippingSettings['carriers'] as $carrierCode => &$carrierData) {
            foreach ($carrierData as $setting => &$properties) {
                if (!is_array($properties)) {
                    continue;
                }

                foreach ($properties as $code => &$values) {
                    if (!isset($values['inputs'])) {
                        continue;
                    }

                    foreach ($values['inputs'] as $inputCode => &$inputValues) {
                        if (!isset($inputValues['defaultConfigValue'])) {
                            continue;
                        }

                        $inputValues['defaultValue'] = (string) $this->scopeConfig->getValue(
                            $inputValues['defaultConfigValue'],
                            ScopeInterface::SCOPE_STORE,
                            $storeId
                        );

                        unset($inputValues['defaultConfigValue']);
                    }
                }
            }
        }

        return $shippingSettings;
    }
}
