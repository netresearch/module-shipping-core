<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Processor\Checkout\ArrayProcessor;

use Netresearch\ShippingCore\Api\ShippingSettings\Processor\Checkout\ShippingOptionsArrayProcessorInterface;

class CheckoutArrayCompositeProcessor
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
     * @param int $storeId
     *
     * @return mixed[]
     */
    public function process(array $shippingData, int $storeId): array
    {
        foreach ($this->shippingOptionsProcessors as $processor) {
            $shippingData = $processor->process($shippingData, $storeId);
        }

        return $shippingData;
    }
}
