<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Rma;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Shipping\Model\Shipment\ReturnShipmentFactory;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;
use Netresearch\ShippingCore\Model\BulkShipment\ReturnShipmentManagement;
use Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment\TrackCollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Request and display return shipment labels.
 */
class Label extends ReturnAction implements HttpPostActionInterface
{
    /**
     * @var OrderProviderInterface
     */
    private $orderProvider;

    /**
     * @var ReturnShipmentFactory
     */
    private $shipmentRequestFactory;

    /**
     * @var ReturnShipmentManagement
     */
    private $returnShipmentManagement;

    /**
     * @var TrackCollectionFactory
     */
    private $trackCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        OrderViewAuthorizationInterface $orderAuthorization,
        OrderProviderInterface $orderProvider,
        CanCreateReturnInterface $canCreateReturn,
        ReturnShipmentFactory $shipmentRequestFactory,
        ReturnShipmentManagement $returnShipmentManagement,
        TrackCollectionFactory $trackCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->orderProvider = $orderProvider;
        $this->shipmentRequestFactory = $shipmentRequestFactory;
        $this->returnShipmentManagement = $returnShipmentManagement;
        $this->trackCollectionFactory = $trackCollectionFactory;
        $this->logger = $logger;

        parent::__construct($context, $orderRepository, $orderAuthorization, $orderProvider, $canCreateReturn);
    }

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        $shipperData = $this->getRequest()->getPost('address', []);
        $shipmentsData = $this->getRequest()->getPost('shipments', []);
        $carrierCode = $this->getRequest()->getPost('carrier_code');
        $order = $this->orderProvider->getOrder();
        $orderNumber = $order->getRealOrderId();

        // we pass any shipment for accessing the order and store in the flow
        $shipment = $order
            ->getShipmentsCollection()
            ->clear()
            ->addFieldToFilter(ShipmentInterface::ENTITY_ID, ['in' => array_keys($shipmentsData)])
            ->getFirstItem();
        $shipment->setData('carrier_code_rma', $carrierCode);

        $requestData = [
            'shipper' => $shipperData,
            'shipments' => $shipmentsData,
            'order_shipment' => $shipment,
        ];
        $request = $this->shipmentRequestFactory->create(['data' => $requestData]);

        try {
            $responses = $this->returnShipmentManagement->createLabels([$request]);
        } catch (LocalizedException $exception) {
            // recoverable user error, add message to UI
            $msg = __('You cannot create a return shipment for order %1: %2.', $orderNumber, $exception->getMessage());
            $this->messageManager->addErrorMessage($msg);
            return $resultRedirect;
        }

        $response = array_shift($responses);
        if ($response instanceof LabelResponseInterface) {
            // load track by order id, then register it for UI
            $trackCollection = $this->trackCollectionFactory->create();
            $trackCollection->setOrderIdFilter((int) $order->getEntityId());
            $trackCollection->setTrackingNumberFilter($response->getTrackingNumber());
            $trackCollection->setPageSize(1)->setCurPage(1)->setOrder(TrackInterface::CREATED_AT);

            /** @var TrackInterface $track */
            $track = $trackCollection->getFirstItem();
            $redirectUrl = $this->getRequest()->getPost('view_url');
            $resultRedirect->setUrl(str_replace('TRACK_ID', (string) $track->getEntityId(), $redirectUrl));
        } elseif ($response instanceof ShipmentErrorResponseInterface) {
            // unrecoverable error, log message
            $this->logger->error($response->getErrors());

            // add generic message to UI
            $this->messageManager->addErrorMessage(__('An error occurred while creating the return shipment label.'));
        }

        return $resultRedirect;
    }
}
