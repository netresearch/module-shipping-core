<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest;

use Magento\Shipping\Model\Shipment\Request;

/**
 * @api
 */
interface RequestModifierInterface
{
    /**
     * Add shipment request data using given shipment.
     *
     * The request modifier collects all additional data from defaults (config, product attributes)
     * during bulk label creation where no user input (packaging popup) is involved.
     *
     * @param Request $shipmentRequest
     */
    public function modify(Request $shipmentRequest): void;
}
