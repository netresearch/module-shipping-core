<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Unit;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\Carrier\CompatibilityPreProcessor;

class PreProcessorMock extends CompatibilityPreProcessor
{
    public function __construct()
    {
    }

    public function process(
        CarrierDataInterface $shippingSettings,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): CarrierDataInterface {
        return $shippingSettings;
    }
}
