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
     * @return bool
     */
    public function isRmaEnabledOnStoreFront(mixed $store = null): bool;

    /**
     * Get the destination address for return shipments.
     *
     * @see \Magento\Rma\Helper\Data::getReturnAddressData
     *
     * @return string[]
     */
    public function getReturnAddress(mixed $store = null): array;
}
