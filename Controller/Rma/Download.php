<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Rma;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentDownloadInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;

/**
 * Download return shipment document from customer account.
 */
class Download implements HttpGetActionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var OrderRepositoryInterface|OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderViewAuthorizationInterface
     */
    private $orderAuthorization;

    /**
     * @var TrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var DocumentDownloadInterface
     */
    private $download;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    public function __construct(
        RequestInterface $request,
        OrderRepositoryInterface $orderRepository,
        OrderViewAuthorizationInterface $orderAuthorization,
        CanCreateReturnInterface $canCreateReturn,
        TrackRepositoryInterface $trackRepository,
        DocumentDownloadInterface $download,
        FileFactory $fileFactory,
        RedirectFactory $redirectFactory,
        MessageManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->orderAuthorization = $orderAuthorization;
        $this->trackRepository = $trackRepository;
        $this->download = $download;
        $this->fileFactory = $fileFactory;
        $this->redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $orderId = (int) $this->request->getParam('order_id', 0);
        $trackId = (int) $this->request->getParam('track_id', 0);
        $documentId = (int) $this->request->getParam('document_id', 0);

        try {
            /** @var Order $order */
            $order = $this->orderRepository->get($orderId);

            if ($this->orderAuthorization->canView($order)) {
                $track = $this->trackRepository->get($trackId);
                $document = $track->getDocument($documentId);

                return $this->fileFactory->create(
                    $this->download->getFileName($document, $track, $order),
                    $document->getLabelData(),
                    DirectoryList::TMP,
                    $document->getMediaType()
                );
            } else {
                $this->messageManager->addErrorMessage(__('This document cannot be loaded.'));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('This document cannot be loaded.'));
        }

        $resultRedirect = $this->redirectFactory->create();
        $resultRedirect->setPath('sales/order/history');
        return $resultRedirect;
    }
}
