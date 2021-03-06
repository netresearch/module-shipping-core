<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Pipeline;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Netresearch\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;

/**
 * Perform action on a rate request as part of running the collect rates pipeline.
 *
 * A pipeline is composed of a sequence of configured stages. One stage performs a certain task on the request object,
 * e.g. validation, transformation, mapping, sending, etc. The pipeline passes an artifacts container into all the
 * stages to store intermediate results.
 *
 * @see ArtifactsContainerInterface
 * @see CollectRatesPipelineInterface
 */
interface CollectRatesStageInterface
{
    /**
     * Perform action on given rate request.
     *
     * @param RateRequest $request
     * @param ArtifactsContainerInterface $artifactsContainer
     * @return void
     * @throws LocalizedException
     */
    public function execute(RateRequest $request, ArtifactsContainerInterface $artifactsContainer): void;
}
