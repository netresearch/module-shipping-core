<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\ReturnShipment\ResponseProcessor;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ReturnShipmentDocumentInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;
use Netresearch\ShippingCore\Model\ReturnShipment\Document;
use Netresearch\ShippingCore\Model\ReturnShipment\DocumentFactory;
use Netresearch\ShippingCore\Model\ReturnShipment\DocumentRepository;
use Netresearch\ShippingCore\Model\ReturnShipment\Track;
use Netresearch\ShippingCore\Model\ReturnShipment\TrackFactory;
use Netresearch\ShippingCore\Model\ReturnShipment\TrackRepository;
use Psr\Log\LoggerInterface;

class SaveTrack implements ShipmentResponseProcessorInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var DocumentRepository
     */
    private $documentRepository;

    /**
     * @var TrackFactory
     */
    private $trackFactory;

    /**
     * @var TrackRepository
     */
    private $trackRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DocumentFactory $documentFactory,
        DocumentRepository $documentRepository,
        TrackFactory $trackFactory,
        TrackRepository $trackRepository,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->documentFactory = $documentFactory;
        $this->documentRepository = $documentRepository;
        $this->trackFactory = $trackFactory;
        $this->trackRepository = $trackRepository;
        $this->logger = $logger;
    }

    /**
     * Persist tracks and documents as retrieved from a return shipment pipeline.
     *
     * @param LabelResponseInterface[] $labelResponses
     * @param ShipmentErrorResponseInterface[] $errorResponses
     */
    public function processResponse(array $labelResponses, array $errorResponses): void
    {
        foreach ($labelResponses as $labelResponse) {
            $trackingNumber = $labelResponse->getTrackingNumber();
            $documents = [];
            foreach ($labelResponse->getDocuments() as $apiDocument) {
                if (!$apiDocument instanceof ReturnShipmentDocumentInterface) {
                    continue;
                }

                if ($apiDocument->getTrackingNumber()) {
                    // the document's tracking number takes precedence.
                    // this should only ever be the case with enclosed return labels.
                    $trackingNumber = $apiDocument->getTrackingNumber();
                }

                $documents[] = $this->documentFactory->create(['data' => [
                    Document::TITLE => $apiDocument->getTitle(),
                    Document::LABEL_DATA => $apiDocument->getLabelData(),
                    Document::MIME_TYPE => $apiDocument->getMimeType()
                ]]);
            }

            if (empty($documents)) {
                continue;
            }

            /** @var Shipment $shipment */
            $shipment = $labelResponse->getSalesShipment();
            $carrierCode = $shipment->getData('carrier_code_rma');
            $carrierTitle = (string) $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                ScopeInterface::SCOPE_STORE,
                $labelResponse->getSalesShipment()->getStoreId()
            );

            $track = $this->trackFactory->create(['data' => [
                Track::ORDER_ID => $labelResponse->getSalesShipment()->getOrderId(),
                Track::CARRIER_CODE => $carrierCode,
                Track::TITLE => $carrierTitle,
                Track::TRACK_NUMBER => $trackingNumber,
            ]]);

            try {
                $this->trackRepository->save($track);
            } catch (CouldNotSaveException $exception) {
                $this->logger->error($exception->getMessage());
                continue;
            }

            array_walk(
                $documents,
                function (Document $document) use ($track) {
                    $document->setData(Document::TRACK_ID, $track->getId());
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
