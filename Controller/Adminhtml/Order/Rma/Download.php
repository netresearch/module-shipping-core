<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Adminhtml\Order\Rma;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentDownloadInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;

class Download extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Magento_Sales::sales_order';

    /**
     * @var TrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var DocumentDownloadInterface
     */
    private $download;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        Context $context,
        TrackRepositoryInterface $trackRepository,
        OrderRepositoryInterface $orderRepository,
        DocumentDownloadInterface $download,
        FileFactory $fileFactory
    ) {
        $this->trackRepository = $trackRepository;
        $this->orderRepository = $orderRepository;
        $this->download = $download;
        $this->fileFactory = $fileFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $trackId = (int) $this->getRequest()->getParam('track_id', 0);
        $documentId = (int) $this->getRequest()->getParam('document_id', 0);

        try {
            $track = $this->trackRepository->get($trackId);
            $document = $track->getDocument($documentId);
            $order = $this->orderRepository->get($track->getOrderId());

            return $this->fileFactory->create(
                $this->download->getFileName($document, $track, $order),
                $document->getLabelData(),
                DirectoryList::TMP,
                $document->getMediaType()
            );
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('This document cannot be loaded.'));

            $resultRedirect = $this->resultRedirectFactory->create();
            if (isset($order)) {
                $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
            } else {
                $resultRedirect->setPath('sales/order/index');
            }

            return $resultRedirect;
        }
    }
}
