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
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentDownloadInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;

/**
 * Download return shipment document from customer account.
 */
class Download extends ReturnAction
{
    /**
     * @var TrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var OrderProviderInterface
     */
    private $orderProvider;

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
        OrderRepositoryInterface $orderRepository,
        OrderViewAuthorizationInterface $orderAuthorization,
        OrderProviderInterface $orderProvider,
        CanCreateReturnInterface $canCreateReturn,
        TrackRepositoryInterface $trackRepository,
        DocumentDownloadInterface $download,
        FileFactory $fileFactory
    ) {
        $this->orderProvider = $orderProvider;
        $this->trackRepository = $trackRepository;
        $this->download = $download;
        $this->fileFactory = $fileFactory;

        parent::__construct($context, $orderRepository, $orderAuthorization, $orderProvider, $canCreateReturn);
    }

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $trackId = (int) $this->getRequest()->getParam('track_id', 0);
        $documentId = (int) $this->getRequest()->getParam('document_id', 0);

        try {
            $track = $this->trackRepository->get($trackId);
            $document = $track->getDocument($documentId);
            $order = $this->orderProvider->getOrder();

            return $this->fileFactory->create(
                $this->download->getFileName($document, $track, $order),
                $document->getLabelData(),
                DirectoryList::TMP,
                $document->getMediaType()
            );
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('This document cannot be loaded.'));

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
