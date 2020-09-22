<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ResponseProcessor;

use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;
use Psr\Log\LoggerInterface;

/**
 * Add shipping label to shipment after api calls.
 */
class AddShippingLabel implements ShipmentResponseProcessorInterface
{
    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LabelGenerator $labelGenerator, LoggerInterface $logger)
    {
        $this->labelGenerator = $labelGenerator;
        $this->logger = $logger;
    }

    public function processResponse(array $labelResponses, array $errorResponses): void
    {
        $shipmentLabels = [];

        // collect all labels per shipment
        foreach ($labelResponses as $labelResponse) {
            /** @var Shipment $shipment */
            $shipment = $labelResponse->getSalesShipment();
            $shipmentLabels[$shipment->getId()][] = $labelResponse->getShippingLabelContent();
        }

        // add combined shipping labels per shipment
        foreach ($labelResponses as $labelResponse) {
            $shipment = $labelResponse->getSalesShipment();
            if (!isset($shipmentLabels[$shipment->getId()])) {
                // labels already processed
                continue;
            }

            try {
                $outputPdf = $this->labelGenerator->combineLabelsPdf($shipmentLabels[$shipment->getId()]);
                $shipment->setShippingLabel($outputPdf->render());
                unset($shipmentLabels[$shipment->getId()]);
            } catch (\Zend_Pdf_Exception $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }
    }
}
