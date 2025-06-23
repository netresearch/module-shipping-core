<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Rate;

/**
 * @api
 */
interface ProxyCarrierConfigInterface
{
    /**
     * Get the code of the carrier to forward rate requests to.
     *
     * @return string
     */
    public function getProxyCarrierCode(mixed $store = null): string;
}
