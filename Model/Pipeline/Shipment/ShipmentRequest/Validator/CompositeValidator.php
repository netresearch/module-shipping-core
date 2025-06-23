<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\Validator;

use Magento\Shipping\Model\Shipment\Request;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestValidatorInterface;

class CompositeValidator implements RequestValidatorInterface
{
    /**
     * @var RequestValidatorInterface[]
     */
    private $validators;

    /**
     * @param RequestValidatorInterface[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    #[\Override]
    public function validate(Request $shipmentRequest): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($shipmentRequest);
        }
    }
}
