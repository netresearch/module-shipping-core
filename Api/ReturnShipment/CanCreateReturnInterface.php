<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ReturnShipment;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Check if a return shipment can be created.
 *
 * @api
 */
interface CanCreateReturnInterface
{
    /**
     * Check if a return shipment can be fulfilled for the given order by any or the specified carrier.
     *
     * @param OrderInterface $order
     * @param string|null $carrierCode
     * @return bool
     */
    public function execute(OrderInterface $order, string $carrierCode = null): bool;
}
