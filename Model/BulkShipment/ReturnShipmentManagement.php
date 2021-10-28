<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\BulkShipment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Shipping\Model\Shipment\ReturnShipment;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentResponseInterface;

class ReturnShipmentManagement
{
    /**
     * @var ReturnShipmentConfiguration
     */
    private $serviceConfig;

    /**
     * @param ReturnShipmentConfiguration $serviceConfig
     */
    public function __construct(ReturnShipmentConfiguration $serviceConfig)
    {
        $this->serviceConfig = $serviceConfig;
    }

    /**
     * @param ReturnShipment[] $returnShipmentRequests
     * @return ShipmentResponseInterface[]
     * @throws LocalizedException
     */
    public function createLabels(array $returnShipmentRequests): array
    {
        $shipmentRequests = [];
        $carrierResults = [];

        // collect all return shipment requests for a carrier (they go to the same API)
        foreach ($returnShipmentRequests as $shipmentRequest) {
            $carrierCode = $shipmentRequest->getOrderShipment()->getData('carrier_code_rma');
            $this->serviceConfig->getRequestModifier($carrierCode)->modify($shipmentRequest);

            $shipmentRequests[$carrierCode][] = $shipmentRequest;
        }

        // send collected return shipment requests to their respective API
        foreach ($shipmentRequests as $carrierCode => $carrierShipmentRequests) {
            $labelService = $this->serviceConfig->getReturnShipmentService($carrierCode);
            $carrierResults[$carrierCode] = $labelService->createLabels($carrierShipmentRequests);
        }

        if (!empty($carrierResults)) {
            // convert results per carrier to flat response
            $carrierResults = array_reduce($carrierResults, 'array_merge', []);
        }

        return $carrierResults;
    }
}
