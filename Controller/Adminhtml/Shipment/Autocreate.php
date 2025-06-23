<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentResponseInterface;
use Netresearch\ShippingCore\Model\BulkShipment\BulkShipmentManagement;

class Autocreate extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Sales::ship';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var BulkShipmentManagement
     */
    private $bulkShipmentManagement;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter,
        BulkShipmentManagement $bulkShipmentManagement
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->bulkShipmentManagement = $bulkShipmentManagement;

        parent::__construct($context);
    }

    /**
     * Receive orders from a mass action and try to create shipments for them via the corresponding API.
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    #[\Override]
    public function execute(): ResultInterface
    {
        // create shipments for requested orders
        $orderCollection = $this->filter->getCollection($this->collectionFactory->create());
        $orderIds = $orderCollection->getColumnValues(OrderInterface::ENTITY_ID);
        $orderIncrementIds = $orderCollection->getColumnValues(OrderInterface::INCREMENT_ID);

        $shipmentIds = $this->bulkShipmentManagement->createShipments($orderIds);

        // extract successfully created shipments, inform about creation errors
        $shipmentIds = array_filter($shipmentIds);
        $failed = array_diff($orderIncrementIds, array_keys($shipmentIds));
        if (!empty($failed)) {
            $this->messageManager->addErrorMessage(
                __('Shipment(s) for the order(s) %1 could not be created.', implode(', ', $failed))
            );
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
                $incrementIds['success'][$orderIncrementId] = $orderIncrementId;
            } else {
                // collect label errors
                $incrementIds['error'][$orderIncrementId] = $orderIncrementId;
                if ($shipmentResponse instanceof ShipmentErrorResponseInterface) {
                    // add error message if details are available
                    $errors = $shipmentResponse->getErrors();
                    $this->messageManager->addErrorMessage(__('Order %1: %2', $orderIncrementId, implode('; ', $errors)));
                }
            }

            return $incrementIds;
        };

        $labelResponses = array_reduce($result, $processResult, ['success' => [], 'error' => []]);

        if (!empty($labelResponses['success'])) {
            // positive webservice responses
            $this->messageManager->addSuccessMessage(
                __('Shipping label(s) for the order(s) %1 were successfully created.', implode(', ', $labelResponses['success']))
            );
        }

        if (!empty($labelResponses['error'])) {
            // negative webservice responses
            $this->messageManager->addErrorMessage(
                __('Shipping label(s) for the order(s) %1 could not be created.', implode(', ', $labelResponses['error']))
            );
        }

        $autoCreateErrors = array_diff(array_keys($shipmentIds), $labelResponses['success'], $labelResponses['error']);
        if (!empty($autoCreateErrors)) {
            // no webservice responses, errors during request preparation
            $this->messageManager->addErrorMessage(
                __('Shipping label(s) for the order(s) %1 could not be requested. Please review shipment comments.', implode(', ', $autoCreateErrors))
            );
        }

        // redirect back to orders grid
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('sales/order');
    }
}
