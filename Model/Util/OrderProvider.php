<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace  Netresearch\ShippingCore\Model\Util;

use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;

class OrderProvider implements OrderProviderInterface
{
    /**
     * @var OrderInterface
     */
    private $order = null;

    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }
}
