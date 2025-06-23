<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ResponseProcessor;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Order\TrackFactory;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;

/**
 * Add track entity to shipment after api calls.
 */
class AddTrack implements ShipmentResponseProcessorInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TrackFactory
     */
    private $trackFactory;

    public function __construct(ScopeConfigInterface $scopeConfig, TrackFactory $trackFactory)
    {
        $this->scopeConfig = $scopeConfig;
        $this->trackFactory = $trackFactory;
    }

    #[\Override]
    public function processResponse(array $labelResponses, array $errorResponses): void
    {
        foreach ($labelResponses as $labelResponse) {
            /** @var Shipment $shipment */
            $shipment = $labelResponse->getSalesShipment();
            $order = $shipment->getOrder();

            $carrierCode = strtok((string)$order->getShippingMethod(), '_');
            $carrierTitle = $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            );

            $track = $this->trackFactory->create();
            $track->setNumber($labelResponse->getTrackingNumber());
            $track->setCarrierCode($carrierCode);
            $track->setTitle($carrierTitle);
            $shipment->addTrack($track);
        }
    }
}
