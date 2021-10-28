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
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentRepositoryInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;

class Download extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Magento_Sales::sales_order';

    /**
     * @var DocumentRepositoryInterface
     */
    private $documentRepository;

    /**
     * @var TrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        Context $context,
        DocumentRepositoryInterface $documentRepository,
        TrackRepositoryInterface $trackRepository,
        OrderRepositoryInterface $orderRepository,
        FileFactory $fileFactory
    ) {
        $this->documentRepository = $documentRepository;
        $this->trackRepository = $trackRepository;
        $this->orderRepository = $orderRepository;
        $this->fileFactory = $fileFactory;

        parent::__construct($context);
    }

    private function getFileName(OrderInterface $order, TrackInterface $track, DocumentInterface $document): string
    {
        switch ($document->getMimeType()) {
            case 'application/pdf':
                $ext = 'pdf';
                break;
            case 'image/png':
                $ext = 'png';
                break;
            default:
                throw new \RuntimeException('File extension for ' . $document->getMimeType() . ' is not defined.');
        }

        return sprintf(
            '%s-%s-%s.%s',
            $order->getIncrementId(),
            $track->getTrackNumber(),
            str_replace(' ', '_', strtolower($document->getTitle())),
            $ext
        );
    }

    public function execute()
    {
        $trackId = (int) $this->getRequest()->getParam('track_id', 0);
        $documentId = (int) $this->getRequest()->getParam('document_id', 0);

        try {
            $document = $this->documentRepository->get($documentId);
            $track = $this->trackRepository->get($trackId);
            $order = $this->orderRepository->get($track->getOrderId());

            return $this->fileFactory->create(
                $this->getFileName($order, $track, $document),
                $document->getLabelData(),
                DirectoryList::ROOT,
                $document->getMimeType()
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
