<?php

namespace Netresearch\ShippingCore\Api\Config;

/**
 * @api
 */
interface RmaConfigInterface
{
    /**
     * Check if the native Magento RMA solution is enabled for customers.
     *
     * @param mixed $store
     * @return bool
     */
    public function isRmaEnabledOnStoreFront($store = null): bool;

    /**
     * Get the destination address for return shipments.
     *
     * @see \Magento\Rma\Helper\Data::getReturnAddressData
     *
     * @param mixed $store
     * @return string[]
     */
    public function getReturnAddress($store = null): array;
}
