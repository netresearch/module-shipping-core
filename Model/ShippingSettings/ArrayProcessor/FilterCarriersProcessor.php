<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\ArrayProcessor;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\ArrayProcessor\ShippingSettingsProcessorInterface;

class FilterCarriersProcessor implements ShippingSettingsProcessorInterface
{
    /**
     * Remove all carrier data that does not apply to the current context.
     *
     * @param mixed[] $shippingSettings
     * @param int $storeId
     * @param ShipmentInterface|null $shipment
     *
     * @return mixed[]
     */
    public function process(array $shippingSettings, int $storeId, ShipmentInterface $shipment = null): array
    {
        if (!$shipment) {
            // no filter criteria available, proceed with all carriers' data
            return $shippingSettings;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        $carrierCode = strtok((string) $order->getShippingMethod(), '_');

        $shippingSettings['carriers'] = array_filter(
            $shippingSettings['carriers'],
            static function (array $carrierSettings) use ($carrierCode) {
                return $carrierSettings['code'] === $carrierCode;
            }
        );

        return $shippingSettings;
    }
}
