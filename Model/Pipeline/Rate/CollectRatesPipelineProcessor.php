<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Rate;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Netresearch\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Netresearch\ShippingCore\Api\Pipeline\CollectRatesStageInterface;

class CollectRatesPipelineProcessor
{
    /**
     * Execute stages, pass previous result to next stage.
     *
     * @param RateRequest $request
     * @param ArtifactsContainerInterface $artifactsContainer
     * @param CollectRatesStageInterface[] $stages
     * @throws LocalizedException
     */
    public function process(RateRequest $request, ArtifactsContainerInterface $artifactsContainer, array $stages): void
    {
        foreach ($stages as $stage) {
            $stage->execute($request, $artifactsContainer);
        }
    }
}
