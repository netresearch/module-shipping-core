<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Util;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

/**
 * Registry for passing a loaded order through the application.
 *
 * @api
 */
interface OrderProviderInterface
{
    public function setOrder(OrderInterface $order): void;

    /**
     * @return OrderInterface|Order|null
     */
    public function getOrder(): ?OrderInterface;
}
