<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline;

use Netresearch\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;

class ArtifactsContainer implements ArtifactsContainerInterface
{
    /**
     * Store id the pipeline runs for.
     *
     * @var int|null
     */
    private $storeId;

    /**
     * Set store id for the pipeline.
     *
     * @param int $storeId
     */
    public function setStoreId(int $storeId): void
    {
        $this->storeId = $storeId;
    }

    /**
     * Get store id for the pipeline.
     *
     * @return int
     */
    public function getStoreId(): int
    {
        return (int) $this->storeId;
    }
}
