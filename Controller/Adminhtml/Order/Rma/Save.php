<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Adminhtml\Order\Rma;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Shipping\Model\Shipment\ReturnShipmentFactory;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;
use Netresearch\ShippingCore\Model\BulkShipment\ReturnShipmentManagement;

class Save extends ReturnAction
{
    public const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

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

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        OrderProviderInterface $orderProvider,
        ReturnShipmentFactory $shipmentRequestFactory,
        ReturnShipmentManagement $returnShipmentManagement
    ) {
        $this->orderProvider = $orderProvider;
        $this->shipmentRequestFactory = $shipmentRequestFactory;
        $this->returnShipmentManagement = $returnShipmentManagement;

        parent::__construct($context, $orderRepository, $orderProvider);
    }

    /**
     * @return ResultInterface
     */
    #[\Override]
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $orderId = $this->getRequest()->getParam('order_id');
        $carrierCode = $this->getRequest()->getPost('carrier_code');
        $shipperData = $this->getRequest()->getPost('address', []);
        $shipmentsData = $this->getRequest()->getPost('shipments', []);

        // we pass any shipment for accessing the order and store in the flow
        // note that the RMA module passes a \Magento\Rma\Model\Shipping model instead
        $shipment = $this->orderProvider->getOrder()
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

        $shipmentRequests = [$this->shipmentRequestFactory->create(['data' => $requestData])];
        try {
            $shipmentResponses = $this->returnShipmentManagement->createLabels($shipmentRequests);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }

        // one request, one response.
        $shipmentResponse = $shipmentResponses[0];


        if ($shipmentResponse instanceof LabelResponseInterface) {
            $message = __(
                'Shipping labels for the order #%1 were successfully created.',
                $this->orderProvider->getOrder()->getIncrementId()
            );
            $this->messageManager->addSuccessMessage($message);
        }

        if ($shipmentResponse instanceof ShipmentErrorResponseInterface) {
            $errors = $shipmentResponse->getErrors();
            $message = __(
                'Shipping labels for the order #%1 could not be created: %2.',
                $this->orderProvider->getOrder()->getIncrementId(),
                implode('; ', $errors)
            );
            $this->messageManager->addErrorMessage($message);
        }

        $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        return $resultRedirect;
    }
}
