<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Netresearch\ShippingCore\Api\Data\Config\ItemValidator\ResultInterface;
use Netresearch\ShippingCore\Api\Data\Config\ValidationResultInterface;

class ValidationResult implements ValidationResultInterface
{
    /**
     * @var ResultInterface[]
     */
    private $results = [];

    #[\Override]
    public function set(array $results): void
    {
        $this->results = $results;
    }

    #[\Override]
    public function get(): array
    {
        return $this->results;
    }
}
