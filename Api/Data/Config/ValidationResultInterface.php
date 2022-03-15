<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Config;

use Netresearch\ShippingCore\Api\Data\Config\ItemValidator\ResultInterface;

/**
 * Container to pass around the config validation result.
 *
 * @api
 */
interface ValidationResultInterface
{
    /**
     * @param ResultInterface[] $results
     */
    public function set(array $results);

    /**
     * @return ResultInterface[]
     */
    public function get(): array;
}
