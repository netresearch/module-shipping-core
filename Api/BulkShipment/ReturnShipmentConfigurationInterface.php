<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\BulkShipment;

use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\ShippingCore\Api\Pipeline\ReturnShipmentRequest\RequestModifierInterface;

/**
 * @api
 */
interface ReturnShipmentConfigurationInterface
{
    /**
     * Obtain the carrier code that the current configuration applies to.
     *
     * @return string
     */
    public function getCarrierCode(): string;

    /**
     * Obtain the carrier's modifier to add carrier specific data to the return shipment request.
     *
     * @return RequestModifierInterface
     */
    public function getRequestModifier(): RequestModifierInterface;

    /**
     * Obtain the service that connects to the carrier's label api for creating return shipment labels.
     *
     * @return ReturnLabelCreationInterface
     */
    public function getLabelService(): ReturnLabelCreationInterface;

    /**
     * Check if the carrier can fulfill a return shipment for the given order.
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function canProcessOrder(OrderInterface $order): bool;
}
