<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Rma;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\Context;
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
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Api\Util\LabelDataProviderInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;
use Netresearch\ShippingCore\Model\BulkShipment\ReturnShipmentManagement;
use Psr\Log\LoggerInterface;

/**
 * Request and display return shipment labels.
 */
class Label extends ReturnAction
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
     * @var LabelDataProviderInterface
     */
    private $labelDataProvider;

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
        LabelDataProviderInterface $labelDataProvider,
        LoggerInterface $logger
    ) {
        $this->orderProvider = $orderProvider;
        $this->shipmentRequestFactory = $shipmentRequestFactory;
        $this->returnShipmentManagement = $returnShipmentManagement;
        $this->labelDataProvider = $labelDataProvider;
        $this->logger = $logger;

        parent::__construct($context, $orderRepository, $orderAuthorization, $orderProvider, $canCreateReturn);
    }

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
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
            $responses = [];
        }

        $response = array_shift($responses);
        if ($response instanceof LabelResponseInterface) {
            // success, register label response for UI and render result page
            $this->labelDataProvider->setLabelResponse($response);

            /** @var Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getConfig()->getTitle()->set(__('Order # %1', $orderNumber));
            $resultPage->getConfig()->getTitle()->prepend(__('Return Labels'));
            return $resultPage;
        }

        if ($response instanceof ShipmentErrorResponseInterface) {
            // unrecoverable error, log message
            $this->logger->error($response->getErrors());
        }

        // add generic message to UI
        $this->messageManager->addErrorMessage(__('An error occurred while creating the return shipment label.'));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
