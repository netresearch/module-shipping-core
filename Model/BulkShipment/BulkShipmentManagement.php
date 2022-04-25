<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\BulkShipment;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Shipping\Model\Shipment\RequestFactory;
use Netresearch\ShippingCore\Api\BulkShipment\OrderLoaderInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;
use Psr\Log\LoggerInterface;

class BulkShipmentManagement
{
    /**
     * @var OrderLoaderInterface
     */
    private $orderLoader;

    /**
     * @var BulkShipmentConfiguration
     */
    private $serviceConfig;

    /**
     * @var ShipmentCollectionLoader
     */
    private $shipmentCollectionLoader;

    /**
     * @var ShipOrderInterface
     */
    private $shipOrder;

    /**
     * @var CancelRequestBuilder
     */
    private $cancelRequestBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var ShipmentNotification
     */
    private $shipmentNotification;

    public function __construct(
        OrderLoaderInterface $orderLoader,
        BulkShipmentConfiguration $serviceConfig,
        ShipmentCollectionLoader $shipmentCollectionLoader,
        ShipOrderInterface $shipOrder,
        CancelRequestBuilder $cancelRequestBuilder,
        LoggerInterface $logger,
        RequestFactory $requestFactory,
        ShipmentNotification $shipmentNotification
    ) {
        $this->orderLoader = $orderLoader;
        $this->serviceConfig = $serviceConfig;
        $this->shipmentCollectionLoader = $shipmentCollectionLoader;
        $this->shipOrder = $shipOrder;
        $this->cancelRequestBuilder = $cancelRequestBuilder;
        $this->logger = $logger;
        $this->requestFactory = $requestFactory;
        $this->shipmentNotification = $shipmentNotification;
    }

    /**
     * Create shipments for given order IDs.
     *
     * @param string[] $orderIds List of selected order ids
     * @return int[][] Created shipment IDs by order increment ID, e.g. ['1000023' => [42, 43], '1000024' => []]
     */
    public function createShipments(array $orderIds): array
    {
        $result = [];
        $orders = $this->orderLoader->load($orderIds);

        foreach ($orders as $order) {
            // query all the order's existing shipments that have no label yet
            $shipmentsCollection = $order->getShipmentsCollection()
                ->addFieldToFilter(ShipmentInterface::SHIPPING_LABEL, ['null' => true]);
            $shipmentIds = $shipmentsCollection->getAllIds();

            // if there are still pending shipments for the order, create them
            if ($order->canShip()) {
                try {
                    $shipmentId = $this->shipOrder->execute($order->getEntityId());
                    $shipmentIds[] = $shipmentId;
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                }
            }

            // collect existing and new shipments
            $result[$order->getIncrementId()] = $shipmentIds;
        }

        return $result;
    }

    /**
     * Create labels for given shipment IDs.
     *
     * @param int[] $shipmentIds
     * @return ShipmentResponseInterface[]
     */
    public function createLabels(array $shipmentIds): array
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $shipmentCollection */
        $shipmentCollection = $this->shipmentCollectionLoader->load($shipmentIds);
        $shipmentRequests = [];
        $carrierResults = [];

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        foreach ($shipmentCollection as $shipment) {
            $order = $shipment->getOrder();
            $carrierCode = strtok((string) $order->getShippingMethod(), '_');

            $shipmentRequest = $this->requestFactory->create();
            $shipmentRequest->setOrderShipment($shipment);

            try {
                $this->serviceConfig->getRequestModifier($carrierCode)->modify($shipmentRequest);
            } catch (\RuntimeException $exception) {
                $shipment->addComment(__('Automatic label creation failed: %1', $exception->getMessage()));
                continue;
            }

            $shipmentRequests[$carrierCode][] = $shipmentRequest;
        }

        foreach ($shipmentRequests as $carrierCode => $carrierShipmentRequests) {
            try {
                $labelService = $this->serviceConfig->getBulkShipmentService($carrierCode);
            } catch (\RuntimeException $exception) {
                $msg = "Bulk label creation is not supported by carrier '$carrierCode'";
                $this->logger->warning($msg, ['exception' => $exception]);
                continue;
            }

            $carrierResults[$carrierCode] = $labelService->createLabels($carrierShipmentRequests);
        }

        // persist labels and tracks added during api action post processing
        $shipmentCollection->save();

        if (!empty($carrierResults)) {
            // convert results per carrier to flat response
            $carrierResults = array_reduce($carrierResults, 'array_merge', []);
        }

        // notify receivers after tracking details were persisted
        $this->shipmentNotification->send($carrierResults);

        return $carrierResults;
    }

    /**
     * Cancel all tracks for given shipment IDs.
     *
     * @param int[] $shipmentIds
     * @return TrackResponseInterface[]
     */
    public function cancelLabels(array $shipmentIds): array
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
                $labelService = $this->serviceConfig->getBulkCancellationService($carrierCode);
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
