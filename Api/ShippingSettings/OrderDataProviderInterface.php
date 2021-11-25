<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings;

use Magento\Sales\Model\Order;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;

/**
 * @api
 */
interface OrderDataProviderInterface
{
    /**
     * @param Order $order
     * @return CarrierDataInterface|null
     */
    public function getShippingOptions(Order $order): ?CarrierDataInterface;
}
