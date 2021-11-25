<?php

namespace Netresearch\ShippingCore\Api\BulkShipment;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Load orders that can be bulk processed.
 *
 * @api
 */
interface OrderLoaderInterface
{
    /**
     * Load orders by given IDs if labels can be booked.
     *
     * @param int[] $orderIds
     * @return OrderInterface[]
     */
    public function load(array $orderIds): array;
}
