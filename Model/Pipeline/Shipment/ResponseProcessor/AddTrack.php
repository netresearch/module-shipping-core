<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ResponseProcessor;

use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Order\TrackFactory;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;

/**
 * Add track entity to shipment after api calls.
 */
class AddTrack implements ShipmentResponseProcessorInterface
{
    /**
     * @var TrackFactory
     */
    private $trackFactory;

    public function __construct(TrackFactory $trackFactory)
    {
        $this->trackFactory = $trackFactory;
    }

    public function processResponse(array $labelResponses, array $errorResponses): void
    {
        foreach ($labelResponses as $labelResponse) {
            /** @var Shipment $shipment */
            $shipment = $labelResponse->getSalesShipment();
            $order = $shipment->getOrder();

            $carrierCode = strtok((string)$order->getShippingMethod(), '_');
            //todo(nr): get carrier title by code here
            $carrierTitle = 'something comes here';

            $track = $this->trackFactory->create();
            $track->setNumber($labelResponse->getTrackingNumber());
            $track->setCarrierCode($carrierCode);
            $track->setTitle($carrierTitle);
            $shipment->addTrack($track);
        }
    }
}
