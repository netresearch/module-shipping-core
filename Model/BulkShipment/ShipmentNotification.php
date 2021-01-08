<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\BulkShipment;

use Magento\Sales\Model\Order\Shipment\NotifierInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentResponseInterface;
use Netresearch\ShippingCore\Model\Config\BatchProcessingConfig;

class ShipmentNotification
{
    /**
     * @var BatchProcessingConfig
     */
    private $config;

    /**
     * @var NotifierInterface
     */
    private $notifier;

    public function __construct(BatchProcessingConfig $config, NotifierInterface $notifier)
    {
        $this->config = $config;
        $this->notifier = $notifier;
    }

    /**
     * Send shipment confirmation email.
     *
     * Skip error responses, check if bulk email notification is enabled via config.
     *
     * @param ShipmentResponseInterface[] $responses
     */
    public function send(array $responses): void
    {
        $isNotificationEnabled = [];

        foreach ($responses as $response) {
            if ($response instanceof ShipmentErrorResponseInterface) {
                continue;
            }

            $shipment = $response->getSalesShipment();
            $storeId = $shipment->getStoreId();

            if (!isset($isNotificationEnabled[$storeId])) {
                $isNotificationEnabled[$storeId] = $this->config->isNotificationEnabled($storeId);
            }

            if ($isNotificationEnabled[$storeId]) {
                $this->notifier->notify($shipment->getOrder(), $shipment);
            }
        }
    }
}
