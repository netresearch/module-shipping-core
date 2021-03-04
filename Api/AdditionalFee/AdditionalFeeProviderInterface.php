<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\AdditionalFee;

interface AdditionalFeeProviderInterface
{
    /**
     * Obtain all configured service adjustment amounts, indexed by shipping option code.
     *
     * Empty values should be omitted.
     *
     * @param int $storeId
     * @return float[]
     */
    public function getAmounts(int $storeId): array;
}
