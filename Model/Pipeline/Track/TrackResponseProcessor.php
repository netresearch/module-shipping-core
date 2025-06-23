<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Track;

use Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackErrorResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;
use Netresearch\ShippingCore\Api\Pipeline\TrackResponseProcessorInterface;

class TrackResponseProcessor implements TrackResponseProcessorInterface
{
    /**
     * @var TrackResponseProcessorInterface[]
     */
    private $processors;

    /**
     * @param TrackResponseProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Perform actions after receiving the "request tracks" response.
     *
     * @param TrackResponseInterface[] $trackResponses
     * @param TrackErrorResponseInterface[] $errorResponses
     * @return void
     */
    #[\Override]
    public function processResponse(array $trackResponses, array $errorResponses): void
    {
        foreach ($this->processors as $processor) {
            $processor->processResponse($trackResponses, $errorResponses);
        }
    }
}
