<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\BulkShipment;

use Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;
use Psr\Log\LoggerInterface;

class BulkShipmentManagement
{
    /**
     * @var BulkShipmentConfiguration
     */
    private $bulkConfig;

    /**
     * @var ShipmentCollectionLoader
     */
    private $shipmentCollectionLoader;

    /**
     * @var CancelRequestBuilder
     */
    private $cancelRequestBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        BulkShipmentConfiguration $bulkConfig,
        ShipmentCollectionLoader $shipmentCollectionLoader,
        CancelRequestBuilder $cancelRequestBuilder,
        LoggerInterface $logger
    ) {
        $this->bulkConfig = $bulkConfig;
        $this->shipmentCollectionLoader = $shipmentCollectionLoader;
        $this->cancelRequestBuilder = $cancelRequestBuilder;
        $this->logger = $logger;
    }

    /**
     * Cancel all tracks for given shipment IDs.
     *
     * @param int[] $shipmentIds
     * @return TrackResponseInterface[]
     */
    public function cancelLabels(array $shipmentIds)
    {
        $shipmentCollection = $this->shipmentCollectionLoader->load($shipmentIds);
        $carrierShipments = [];
        $carrierResults = [];

        // divide shipments by carrier code
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        foreach ($shipmentCollection as $shipment) {
            $order = $shipment->getOrder();
            $carrierCode = strtok((string)$order->getShippingMethod(), '_');

            $carrierShipments[$carrierCode][] = $shipment;
        }

        // cancel tracks per carrier
        foreach ($carrierShipments as $carrierCode => $shipments) {
            $this->cancelRequestBuilder->setShipments($shipments);
            $cancelRequests = $this->cancelRequestBuilder->build($carrierCode);

            try {
                $labelService = $this->bulkConfig->getBulkCancellationService($carrierCode);
            } catch (\RuntimeException $exception) {
                $msg = "Bulk label cancellation is not supported by carrier '$carrierCode'";
                $this->logger->warning($msg, ['exception' => $exception]);
                continue;
            }

            $carrierResults[$carrierCode] = $labelService->cancelLabels($cancelRequests);
        }

        if (!empty($carrierResults)) {
            // convert results per carrier to flat response
            $carrierResults = array_reduce($carrierResults, 'array_merge', []);
        }

        return $carrierResults;
    }
}
