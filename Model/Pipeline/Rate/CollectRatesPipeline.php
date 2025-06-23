<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Rate;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Netresearch\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterfaceFactory;
use Netresearch\ShippingCore\Api\Pipeline\CollectRatesPipelineInterface;
use Netresearch\ShippingCore\Api\Pipeline\CollectRatesStageInterface;

class CollectRatesPipeline implements CollectRatesPipelineInterface
{
    /**
     * @var CollectRatesPipelineProcessor
     */
    private $pipelineProcessor;

    /**
     * @var ArtifactsContainerInterfaceFactory
     */
    private $artifactsContainerFactory;

    /**
     * @var CollectRatesStageInterface[]
     */
    private $stages;

    public function __construct(
        CollectRatesPipelineProcessor $pipelineProcessor,
        ArtifactsContainerInterfaceFactory $artifactsContainerFactory,
        array $stages = []
    ) {
        $this->pipelineProcessor = $pipelineProcessor;
        $this->artifactsContainerFactory = $artifactsContainerFactory;
        $this->stages = $stages;
    }

    #[\Override]
    public function run(int $storeId, RateRequest $request): ArtifactsContainerInterface
    {
        $artifactsContainer = $this->artifactsContainerFactory->create();
        $artifactsContainer->setStoreId($storeId);

        $this->pipelineProcessor->process($request, $artifactsContainer, $this->stages);

        return $artifactsContainer;
    }
}
