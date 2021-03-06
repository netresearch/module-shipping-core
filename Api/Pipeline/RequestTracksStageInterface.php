<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Pipeline;

use Netresearch\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\TrackRequest\TrackRequestInterface;

/**
 * Perform action on track requests as part of running the request tracks pipeline.
 *
 * A pipeline is composed of a sequence of configured stages. Each stage performs a certain task on the request object,
 * e.g. validation, transformation, mapping, sending, etc. The pipeline passes an artifacts container into all the
 * stages to store intermediate results.
 *
 * @see ArtifactsContainerInterface
 * @see RequestTracksPipelineInterface
 *
 * @api
 */
interface RequestTracksStageInterface
{
    /**
     * Perform action on given track requests.
     *
     * @param TrackRequestInterface[] $requests
     * @param ArtifactsContainerInterface $artifactsContainer
     * @return TrackRequestInterface[]
     */
    public function execute(array $requests, ArtifactsContainerInterface $artifactsContainer): array;
}
