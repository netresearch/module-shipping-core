<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse;

use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * A response created during pipeline execution.
 *
 * @api
 */
interface ShipmentResponseInterface
{
    public const REQUEST_INDEX = 'request_index';
    public const SALES_SHIPMENT = 'sales_shipment';

    /**
     * Obtain request index (unique package id, sequence number).
     *
     * @return string
     */
    public function getRequestIndex(): string;

    /**
     * Get the shipment that the label is requested for.
     *
     * @return ShipmentInterface
     */
    public function getSalesShipment(): ShipmentInterface;
}
