<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment;

use Netresearch\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;

class ShipmentResponseProcessor implements ShipmentResponseProcessorInterface
{
    /**
     * @var ShipmentResponseProcessorInterface[]
     */
    private $processors;

    /**
     * @param ShipmentResponseProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    #[\Override]
    public function processResponse(array $labelResponses, array $errorResponses): void
    {
        foreach ($this->processors as $processor) {
            $processor->processResponse($labelResponses, $errorResponses);
        }
    }
}
