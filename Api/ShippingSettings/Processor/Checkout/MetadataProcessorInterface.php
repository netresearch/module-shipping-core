<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\Processor\Checkout;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;

/**
 * @api
 */
interface MetadataProcessorInterface
{
    /**
     * Receive shipping option metadata and modify it according to business logic.
     *
     * @param MetadataInterface $metadata
     * @param int|null $storeId
     *
     * @return MetadataInterface
     */
    public function process(MetadataInterface $metadata, int $storeId = null): MetadataInterface;
}
