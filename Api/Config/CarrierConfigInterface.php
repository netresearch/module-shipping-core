<?php

namespace Netresearch\ShippingCore\Api\Config;

/**
 * @api
 */
interface CarrierConfigInterface
{
    /**
     * Check if a carrier, identified by given carrier code, is enabled for checkout.
     *
     * @param string $carrierCode
     * @return bool
     */
    public function isActive(string $carrierCode, mixed $store = null): bool;

    /**
     * Obtain carrier title for given carrier code.
     *
     * @param string $carrierCode
     * @return string
     */
    public function getTitle(string $carrierCode, mixed $store = null): string;
}
