<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Cron;

use Magento\Framework\Data\Collection\AbstractDb;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentResponseInterface;
use Netresearch\ShippingCore\Cron\AutoCreate\OrderCollectionLoader;
use Netresearch\ShippingCore\Cron\AutoCreate\OrderCollectionLoader\AutoCreateDisabledException;
use Netresearch\ShippingCore\Model\BulkShipment\BulkShipmentConfiguration;
use Netresearch\ShippingCore\Model\BulkShipment\BulkShipmentManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

/**
 * Cron entry point for automatic shipment creation and label retrieval.
 */
class AutoCreate
{
    /**
     * @var BulkShipmentConfiguration
     */
    private $bulkConfig;

    /**
     * @var OrderCollectionLoader
     */
    private $orderCollectionLoader;

    /**
     * @var BulkShipmentManagement
     */
    private $bulkShipmentManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        BulkShipmentConfiguration $bulkConfig,
        OrderCollectionLoader $orderCollectionLoader,
        BulkShipmentManagement $bulkShipmentManagement,
        LoggerInterface $logger
    ) {
        $this->bulkConfig = $bulkConfig;
        $this->orderCollectionLoader = $orderCollectionLoader;
        $this->bulkShipmentManagement = $bulkShipmentManagement;
        $this->logger = $logger;
    }

    /**
     * Collect orders with pending labels and try to create shipments for them via the corresponding API.
     */
    public function execute(): void
    {
        $fnFilter = function (string $carrierCode) {
            try {
                return $this->bulkConfig->getBulkShipmentService($carrierCode);
            } catch (\RuntimeException $exception) {
                return false;
            }
        };

        $carrierCodes = array_filter($this->bulkConfig->getCarrierCodes(), $fnFilter);

        try {
            $orderCollection = $this->orderCollectionLoader->load($carrierCodes);
        } catch (AutoCreateDisabledException $exception) {
            $this->logger->debug($exception->getLogMessage());
            return;
        } catch (NoSuchEntityException $exception) {
            $this->logger->warning($exception->getLogMessage());
            return;
        }

        $orderIds = $orderCollection->getColumnValues(OrderInterface::ENTITY_ID);
        $orderIncrementIds = $orderCollection->getColumnValues(OrderInterface::INCREMENT_ID);

        $shipmentIds = $this->bulkShipmentManagement->createShipments($orderIds);

        // extract successfully created shipments, inform about creation errors
        $shipmentIds = array_filter($shipmentIds);
        $failed = array_diff($orderIncrementIds, array_keys($shipmentIds));
        if (!empty($failed)) {
            $message = sprintf('Shipment(s) for the order(s) %s could not be created.', implode(', ', $failed));
            $this->logger->error($message);
        }

        // create labels and tracks for above shipments
        $result = $this->bulkShipmentManagement->createLabels(array_reduce($shipmentIds, 'array_merge', []));

        // check result, inform about created and failed labels/tracks
        $processResult = function (array $incrementIds, ShipmentResponseInterface $shipmentResponse) {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $shipment = $shipmentResponse->getSalesShipment();
            $orderIncrementId = $shipment->getOrder()->getIncrementId();

            if ($shipmentResponse instanceof LabelResponseInterface
                && !empty($shipment->getShippingLabel())
                && !empty($shipment->getTracks())
            ) {
                // collect successfully created labels
                $incrementIds['success'][] = $orderIncrementId;
            } else {
                // collect label errors
                $incrementIds['error'][] = $orderIncrementId;
                if ($shipmentResponse instanceof ShipmentErrorResponseInterface) {
                    // add error message if details are available
                    $message = sprintf('Order %s: %s.', $orderIncrementId, $shipmentResponse->getErrors());
                    $this->logger->error($message);
                }
            }

            return $incrementIds;
        };

        $labelResponses = array_reduce($result, $processResult, ['success' => [], 'error' => []]);

        if (!empty($labelResponses['success'])) {
            // positive webservice responses
            $message = sprintf(
                'Shipping label(s) for the order(s) %s were successfully created.',
                implode(', ', $labelResponses['success'])
            );
            $this->logger->info($message);
        }

        if (!empty($labelResponses['error'])) {
            // negative webservice responses
            $message = sprintf(
                'Shipping label(s) for the order(s) %s could not be created.',
                implode(', ', $labelResponses['error'])
            );
            $this->logger->error($message);
        }

        $autoCreateErrors = array_diff(array_keys($shipmentIds), $labelResponses['success'], $labelResponses['error']);
        if (!empty($autoCreateErrors)) {
            // no webservice responses, errors during request preparation
            $message = sprintf(
                'Shipping label(s) for the order(s) %s could not be requested. Please review shipment comments.',
                implode(', ', $autoCreateErrors)
            );
            $this->logger->error($message);
        }
    }
}
