<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Rma;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentRepositoryInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;

/**
 * Download return shipment document from customer account.
 */
class Download extends ReturnAction
{
    /**
     * @var DocumentRepositoryInterface
     */
    private $documentRepository;

    /**
     * @var TrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var OrderProviderInterface
     */
    private $orderProvider;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        OrderViewAuthorizationInterface $orderAuthorization,
        OrderProviderInterface $orderProvider,
        CanCreateReturnInterface $canCreateReturn,
        DocumentRepositoryInterface $documentRepository,
        TrackRepositoryInterface $trackRepository,
        FileFactory $fileFactory
    ) {
        $this->orderProvider = $orderProvider;
        $this->documentRepository = $documentRepository;
        $this->trackRepository = $trackRepository;
        $this->fileFactory = $fileFactory;

        parent::__construct($context, $orderRepository, $orderAuthorization, $orderProvider, $canCreateReturn);
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

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $trackId = (int) $this->getRequest()->getParam('track_id', 0);
        $documentId = (int) $this->getRequest()->getParam('document_id', 0);

        try {
            $document = $this->documentRepository->get($documentId);
            $track = $this->trackRepository->get($trackId);
            $order = $this->orderProvider->getOrder();

            return $this->fileFactory->create(
                $this->getFileName($order, $track, $document),
                $document->getLabelData(),
                DirectoryList::TMP,
                $document->getMimeType()
            );
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('This document cannot be loaded.'));

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
