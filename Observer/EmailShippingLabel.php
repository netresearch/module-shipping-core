<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment;
use Netresearch\ShippingCore\Api\Config\ParcelProcessingConfigInterface;
use Netresearch\ShippingCore\Model\Email\Shipment\TransportBuilder;
use Psr\Log\LoggerInterface;

class EmailShippingLabel implements ObserverInterface
{
    /**
     * @var ParcelProcessingConfigInterface
     */
    private $config;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string[]
     */
    private $carrierCodes;

    public function __construct(
        ParcelProcessingConfigInterface $config,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger,
        array $carrierCodes = []
    ) {
        $this->config = $config;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->carrierCodes = $carrierCodes;
    }

    #[\Override]
    public function execute(Observer $observer)
    {
        /** @var Shipment $shipment */
        $shipment = $observer->getData('shipment');
        if (!$shipment instanceof ShipmentInterface) {
            // shipment entity not available, abort.
            return;
        }

        if (!$this->config->isShippingLabelEmailEnabled($shipment->getStoreId())) {
            return;
        }

        $carrierCode = strtok((string) $shipment->getOrder()->getShippingMethod(), '_');
        if (!in_array($carrierCode, $this->carrierCodes)) {
            return;
        }

        $originalLabel = $shipment->getOrigData(ShipmentInterface::SHIPPING_LABEL);
        $label = $shipment->getData(ShipmentInterface::SHIPPING_LABEL);
        if (!$label || $label === $originalLabel) {
            // no (new) label, abort.
            return;
        }

        try {
            $transport = $this->transportBuilder
                ->setShipment($shipment)
                ->setReceiverEmail($this->config->getShippingLabelEmailAddress($shipment->getStoreId()))
                ->build();
            $transport->sendMessage();
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
        }
    }
}
