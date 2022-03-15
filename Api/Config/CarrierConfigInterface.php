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
     * @param mixed $store
     * @return bool
     */
    public function isActive(string $carrierCode, $store = null): bool;

    /**
     * Obtain carrier title for given carrier code.
     *
     * @param string $carrierCode
     * @param mixed $store
     * @return string
     */
    public function getTitle(string $carrierCode, $store = null): string;
}
