<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings\Processor\Checkout;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;

/**
 * @api
 */
interface GlobalProcessorInterface
{
    /**
     * @param CarrierDataInterface $carrierData
     * @return CarrierDataInterface
     *
     * @throws \InvalidArgumentException
     */
    public function process(CarrierDataInterface $carrierData): CarrierDataInterface;
}
