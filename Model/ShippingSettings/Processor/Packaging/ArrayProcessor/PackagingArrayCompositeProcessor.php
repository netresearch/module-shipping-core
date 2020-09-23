<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Processor\Packaging\ArrayProcessor;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsArrayProcessorInterface;

class PackagingArrayCompositeProcessor
{
    /**
     * @var ShippingOptionsArrayProcessorInterface[]
     */
    private $shippingOptionsProcessors;

    public function __construct(array $shippingOptionsProcessors = [])
    {
        $this->shippingOptionsProcessors = $shippingOptionsProcessors;
    }

    /**
     * Receive an array of shipping option data and modify it according to business logic.
     *
     * @param mixed[] $shippingData
     * @param ShipmentInterface $shipment
     *
     * @return mixed[]
     */
    public function process(array $shippingData, ShipmentInterface $shipment): array
    {
        foreach ($this->shippingOptionsProcessors as $processor) {
            $shippingData = $processor->process($shippingData, $shipment);
        }

        return $shippingData;
    }
}
