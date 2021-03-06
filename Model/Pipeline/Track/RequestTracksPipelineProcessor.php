<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Track;

use Netresearch\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\TrackRequest\TrackRequestInterface;
use Netresearch\ShippingCore\Api\Pipeline\RequestTracksStageInterface;

class RequestTracksPipelineProcessor
{
    /**
     * Execute stages, pass previous result to next stage.
     *
     * @param TrackRequestInterface[] $requests
     * @param RequestTracksStageInterface[] $stages
     * @param ArtifactsContainerInterface $artifactsContainer
     * @return TrackRequestInterface[]
     */
    public function process(array $requests, array $stages, ArtifactsContainerInterface $artifactsContainer): array
    {
        foreach ($stages as $stage) {
            $requests = $stage->execute($requests, $artifactsContainer);
        }

        return $requests;
    }
}
