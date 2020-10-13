<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\ArrayProcessor;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\ArrayProcessor\ShippingSettingsProcessorInterface;

class CompositeShippingSettingsProcessor implements ShippingSettingsProcessorInterface
{
    /**
     * @var ShippingSettingsProcessorInterface[]
     */
    private $processors;

    /**
     * CompositeDataProcessor constructor.
     *
     * @param ShippingSettingsProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    public function process(array $shippingSettings, int $storeId, ShipmentInterface $shipment = null): array
    {
        foreach ($this->processors as $processor) {
            $shippingSettings = $processor->process($shippingSettings, $storeId, $shipment);
        }

        return $shippingSettings;
    }
}
