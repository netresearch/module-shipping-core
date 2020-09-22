<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Shipping\Model\Shipment\Request;
use Magento\Shipping\Model\Shipment\ReturnShipment;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestValidatorInterface;

/**
 * Validate that no return shipment label is requested.
 */
class NoReturnValidator implements RequestValidatorInterface
{
    public function validate(Request $shipmentRequest): void
    {
        if (($shipmentRequest->getData('is_return') || $shipmentRequest instanceof ReturnShipment)) {
            throw new ValidatorException(__('Return shipments are not supported.'));
        }
    }
}
