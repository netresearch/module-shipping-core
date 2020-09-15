<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Rate;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Netresearch\ShippingCore\Api\Pipeline\RateResponseProcessorInterface;

class RateResponseProcessor implements RateResponseProcessorInterface
{
    /**
     * @var RateResponseProcessorInterface[]
     */
    private $processors;

    /**
     * RateResponseProcessor constructor.
     *
     * @param RateResponseProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Perform read/write actions on the webservice rate result.
     *
     * @param Method[] $methods
     * @param RateRequest|null $request
     * @return Method[]
     */
    public function processMethods(array $methods, RateRequest $request = null): array
    {
        foreach ($this->processors as $processor) {
            $methods = $processor->processMethods($methods, $request);
        }

        return $methods;
    }
}
