<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest;

use Magento\Framework\Exception\ValidatorException;
use Magento\Shipping\Model\Shipment\Request;

/**
 * @api
 */
interface RequestValidatorInterface
{
    /**
     * Validate shipment requests and throw exception on errors.
     *
     * @param Request $shipmentRequest
     * @throws ValidatorException
     *
     * @return void
     */
    public function validate(Request $shipmentRequest): void;
}
