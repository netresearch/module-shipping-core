<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Observer;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\Order\Shipment\TrackRepository;
use Magento\Shipping\Controller\Adminhtml\Order\Shipment\RemoveTrack;
use Netresearch\ShippingCore\Model\BulkShipment\BulkShipmentConfiguration;

/**
 * Prevent tracks being deleted without cancelling the shipment/removing the label.
 */
class PreventTrackDeletion implements ObserverInterface
{
    /**
     * @var TrackRepository
     */
    private $trackRepository;

    /**
     * @var BulkShipmentConfiguration
     */
    private $bulkConfig;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        TrackRepository $trackRepository,
        BulkShipmentConfiguration $bulkConfig,
        ActionFlag $actionFlag,
        SerializerInterface $serializer
    ) {
        $this->trackRepository = $trackRepository;
        $this->bulkConfig = $bulkConfig;
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
    }

    /**
     * Prohibit the deletion of individual shipment tracking numbers.
     *
     * Carrier modules must register themselves to use this feature.
     * @see \Netresearch\ShippingCore\Model\BulkShipment\BulkShipmentConfiguration::isSingleTrackDeletionAllowed
     *
     * Event:
     * - controller_action_predispatch_adminhtml_order_shipment_removeTrack
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Request $request */
        $request = $observer->getData('request');
        $trackId = (int) $request->getParam('track_id');

        $track = $this->trackRepository->get($trackId);

        try {
            $isAllowed = $this->bulkConfig->isSingleTrackDeletionAllowed($track->getCarrierCode());
        } catch (\RuntimeException $exception) {
            // no restrictions given, continue with default behaviour
            return;
        }

        if ($isAllowed) {
            // single track deletion allowed, continue with default behaviour
            return;
        }

        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        $response = $this->serializer->serialize([
            'error' => true,
            'message' => __('Deleting a single tracking number is not supported. Please use the "Cancel Labels" button on the shipment details page to cancel labels and tracks.')
        ]);

        /** @var RemoveTrack $controller */
        $controller = $observer->getData('controller_action');

        /** @var Http $actionResponse */
        $actionResponse = $controller->getResponse();
        $actionResponse->representJson($response);
    }
}
