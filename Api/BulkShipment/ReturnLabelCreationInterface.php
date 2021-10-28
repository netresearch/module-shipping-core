<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\BulkShipment;

use Magento\Shipping\Model\Shipment\ReturnShipment;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentResponseInterface;

/**
 * @api
 */
interface ReturnLabelCreationInterface
{
    /**
     * Create return shipment labels for given shipment requests.
     *
     * @param ReturnShipment[] $shipmentRequests
     * @return ShipmentResponseInterface[]
     */
    public function createLabels(array $shipmentRequests): array;
}
