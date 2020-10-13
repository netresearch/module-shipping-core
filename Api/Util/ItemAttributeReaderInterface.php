<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Util;

use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * @api
 */
interface ItemAttributeReaderInterface
{
    /**
     * Read HS code from order item.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getHsCode(OrderItemInterface $orderItem): string;

    /**
     * Read country of manufacture from order item.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getCountryOfManufacture(OrderItemInterface $orderItem): string;
}
