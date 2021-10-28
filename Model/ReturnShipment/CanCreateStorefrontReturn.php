<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment;

use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\ShippingCore\Api\Config\RmaConfigInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;

class CanCreateStorefrontReturn implements CanCreateReturnInterface
{
    /**
     * @var RmaConfigInterface
     */
    private $rmaConfig;

    /**
     * @var CanCreateReturn
     */
    private $canCreateReturn;

    public function __construct(RmaConfigInterface $rmaConfig, CanCreateReturn $canCreateReturn)
    {
        $this->rmaConfig = $rmaConfig;
        $this->canCreateReturn = $canCreateReturn;
    }

    /**
     * Check if a return shipment can be fulfilled for the given order by any or the specified carrier.
     *
     * In storefront, the feature is not enabled if the core RMA module provides it.
     *
     * @param OrderInterface $order
     * @param string|null $carrierCode
     * @return bool
     */
    public function execute(OrderInterface $order, string $carrierCode = null): bool
    {
        return !$this->rmaConfig->isRmaEnabledOnStoreFront($order->getStoreId())
            && $this->canCreateReturn->execute($order, $carrierCode);
    }
}
