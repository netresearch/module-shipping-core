<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\BulkShipment;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Model\Order;
use Magento\Shipping\Model\Shipment\RequestFactory;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;
use Netresearch\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Netresearch\ShippingCore\Model\Config\BatchProcessingConfig;
use Netresearch\ShippingCore\Model\LabelStatus\LabelStatusProvider;
use Psr\Log\LoggerInterface;

class BulkShipmentManagement
{
    /**
     * @var BatchProcessingConfig
     */
    private $batchConfig;

    /**
     * @var BulkShipmentConfiguration
     */
    private $serviceConfig;

    /**
     * @var OrderCollectionLoader
     */
    private $orderCollectionLoader;

    /**
     * @var ShipmentCollectionLoader
     */
    private $shipmentCollectionLoader;

    /**
     * @var LabelStatusProvider
     */
    private $labelStatusProvider;

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

    public function __construct(
        BatchProcessingConfig $batchConfig,
        BulkShipmentConfiguration $serviceConfig,
        OrderCollectionLoader $orderCollectionLoader,
        ShipmentCollectionLoader $shipmentCollectionLoader,
        LabelStatusProvider $labelStatusProvider,
        ShipOrderInterface $shipOrder,
        CancelRequestBuilder $cancelRequestBuilder,
        LoggerInterface $logger,
        RequestFactory $requestFactory
    ) {
        $this->batchConfig = $batchConfig;
        $this->serviceConfig = $serviceConfig;
        $this->orderCollectionLoader = $orderCollectionLoader;
        $this->shipmentCollectionLoader = $shipmentCollectionLoader;
        $this->labelStatusProvider = $labelStatusProvider;
        $this->shipOrder = $shipOrder;
        $this->cancelRequestBuilder = $cancelRequestBuilder;
        $this->logger = $logger;
        $this->requestFactory = $requestFactory;
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

        $retryFailedShipments = $this->batchConfig->isRetryEnabled();

        $fnFilter = function (string $carrierCode) {
            try {
                return $this->serviceConfig->getBulkShipmentService($carrierCode);
            } catch (\RuntimeException $exception) {
                return false;
            }
        };

        $carrierCodes = array_filter($this->serviceConfig->getCarrierCodes(), $fnFilter);
        $orders = $this->orderCollectionLoader->load($orderIds, $carrierCodes);
        $ordersLabelStatus = $this->labelStatusProvider->getLabelStatus($orderIds);

        /** @var Order $order */
        foreach ($orders as $order) {
            $notify = $this->batchConfig->isNotificationEnabled($order->getStoreId());
            $shipmentsCollection = $order->getShipmentsCollection()
                ->addFieldToFilter(ShipmentInterface::SHIPPING_LABEL, ['null' => true]);

            $labelStatus = $ordersLabelStatus[$order->getId()] ?? null;
            if ($retryFailedShipments || $labelStatus !== LabelStatusManagementInterface::LABEL_STATUS_FAILED) {
                $shipmentIds = $shipmentsCollection->getAllIds();
            } else {
                $shipmentIds = [];
            }

            if ($order->canShip()) {
                try {
                    $shipmentId = $this->shipOrder->execute($order->getId(), [], $notify);
                    $shipmentIds[] = $shipmentId;
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                }
            }

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
    public function createLabels(array $shipmentIds)
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

        return $carrierResults;
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
