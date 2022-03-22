<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ResponseProcessor;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order\Shipment;
use Netresearch\ShippingCore\Api\Config\CarrierConfigInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ReturnShipmentDocumentInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentDocumentInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterfaceFactory;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentRepositoryInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Psr\Log\LoggerInterface;

class SaveReturnShipmentDocuments implements ShipmentResponseProcessorInterface
{
    /**
     * @var CarrierConfigInterface
     */
    private $config;

    /**
     * @var TrackInterfaceFactory
     */
    private $trackFactory;

    /**
     * @var DocumentInterfaceFactory
     */
    private $documentFactory;

    /**
     * @var TrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var DocumentRepositoryInterface
     */
    private $documentRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CarrierConfigInterface $config,
        TrackInterfaceFactory $trackFactory,
        DocumentInterfaceFactory $documentFactory,
        TrackRepositoryInterface $trackRepository,
        DocumentRepositoryInterface $documentRepository,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->trackFactory = $trackFactory;
        $this->documentFactory = $documentFactory;
        $this->trackRepository = $trackRepository;
        $this->documentRepository = $documentRepository;
        $this->logger = $logger;
    }

    /**
     * Save return shipment tracks and their documents if included with the label responses.
     *
     * One label response belongs to one shipment request. One label response
     * can hold multiple response documents, some of them may be used for
     * returning a delivery. Although the return documents should all have
     * the same tracking number assigned, there is no guarantee. Therefore,
     * we group all the return shipment documents by tracking number and persist
     * one track each along with its documents.
     *
     * @param LabelResponseInterface[] $labelResponses
     * @param ShipmentErrorResponseInterface[] $errorResponses
     * @return void
     */
    public function processResponse(array $labelResponses, array $errorResponses): void
    {
        foreach ($labelResponses as $labelResponse) {
            // group return shipment documents by tracking number.
            $labelResponseDocuments = array_reduce(
                $labelResponse->getDocuments(),
                static function (array $documents, ShipmentDocumentInterface $document) {
                    if ($document instanceof ReturnShipmentDocumentInterface) {
                        if (!isset($documents[$document->getTrackingNumber()])) {
                            $documents[$document->getTrackingNumber()] = [$document];
                        } else {
                            $documents[$document->getTrackingNumber()][] = $document;
                        }
                    }

                    return $documents;
                },
                []
            );

            // if there are no return shipment documents in this shipment label response, next.
            if (empty($labelResponseDocuments)) {
                continue;
            }

            /** @var Shipment $shipment */
            $shipment = $labelResponse->getSalesShipment();
            $order = $shipment->getOrder();
            $carrierCode = strtok((string)$order->getShippingMethod(), '_');

            foreach ($labelResponseDocuments as $trackingNumber => $trackDocuments) {
                // create and persists one track per tracking number
                $track = $this->trackFactory->create([
                    'data' => [
                        TrackInterface::ORDER_ID => $order->getEntityId(),
                        TrackInterface::CARRIER_CODE => $carrierCode,
                        TrackInterface::TITLE => $this->config->getTitle($carrierCode),
                        TrackInterface::TRACK_NUMBER => $trackingNumber,
                    ]
                ]);

                try {
                    $this->trackRepository->save($track);
                } catch (CouldNotSaveException $exception) {
                    $this->logger->error($exception->getMessage());
                    continue;
                }

                // persist label data per document
                array_walk(
                    $trackDocuments,
                    function (ReturnShipmentDocumentInterface $labelResponseDocument) use ($track) {
                        $document = $this->documentFactory->create([
                            'data' => [
                                DocumentInterface::TRACK_ID => $track->getId(),
                                DocumentInterface::TITLE => $labelResponseDocument->getTitle(),
                                DocumentInterface::LABEL_DATA => base64_decode($labelResponseDocument->getLabelData()),
                                DocumentInterface::MIME_TYPE => $labelResponseDocument->getMimeType(),
                            ]
                        ]);

                        try {
                            $this->documentRepository->save($document);
                        } catch (CouldNotSaveException $exception) {
                            $this->logger->error($exception->getMessage());
                        }
                    }
                );
            }
        }
    }
}
