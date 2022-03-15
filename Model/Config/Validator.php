<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Netresearch\ShippingCore\Api\Config\ItemValidatorInterface;
use Netresearch\ShippingCore\Api\Data\Config\ItemValidator\ResultInterface;

/**
 * The central config validator that collects and executes all registered item validators.
 *
 * @api
 */
class Validator
{
    /**
     * @var ItemValidatorInterface[]
     */
    private $itemValidators;

    /**
     * @param ItemValidatorInterface[] $itemValidators
     */
    public function __construct(array $itemValidators = [])
    {
        $this->itemValidators = $itemValidators;
    }

    /**
     * @return ResultInterface[]
     */
    public function execute(int $storeId, string $section = ''): array
    {
        $results = [];

        foreach ($this->itemValidators as $validator) {
            if (!$section || $validator->getSectionCode() === $section) {
                $results[] = $validator->execute($storeId);
            }
        }

        return $results;
    }
}
