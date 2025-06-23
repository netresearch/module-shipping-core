<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\RequestExtractor;

use Magento\Shipping\Model\Shipment\Request;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestExtractor\ServiceOptionReaderInterface;

class ServiceOptionReader implements ServiceOptionReaderInterface
{
    /**
     * @var Request
     */
    private $shipmentRequest;

    public function __construct(Request $shipmentRequest)
    {
        $this->shipmentRequest = $shipmentRequest;
    }

    #[\Override]
    public function getServiceOptionValue(string $optionCode, string $inputCode)
    {
        $packages = $this->shipmentRequest->getData('packages');
        $packageId = $this->shipmentRequest->getData('package_id');
        $option = $packages[$packageId]['params']['services'][$optionCode] ?? [];

        return $option[$inputCode] ?? '';
    }

    #[\Override]
    public function isServiceEnabled(string $optionCode, string $inputCode = 'enabled'): bool
    {
        return (bool) $this->getServiceOptionValue($optionCode, $inputCode);
    }
}
