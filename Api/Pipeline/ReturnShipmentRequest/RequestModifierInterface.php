<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Pipeline\ReturnShipmentRequest;

use Magento\Framework\Exception\LocalizedException;
use Magento\Shipping\Model\Shipment\ReturnShipment;

/**
 * @api
 */
interface RequestModifierInterface
{
    /**
     * @param ReturnShipment $shipmentRequest
     * @throws LocalizedException
     */
    public function modify(ReturnShipment $shipmentRequest): void;
}
