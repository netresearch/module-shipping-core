<?php

namespace Netresearch\ShippingCore\Api\Config;

/**
 * @api
 */
interface CarrierConfigInterface
{
    /**
     * Obtain carrier title for given carrier code.
     *
     * @param string $carrierCode
     * @param mixed $store
     * @return string
     */
    public function getTitle(string $carrierCode, $store = null): string;
}
