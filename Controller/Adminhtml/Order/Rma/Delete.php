<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Adminhtml\Order\Rma;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Psr\Log\LoggerInterface;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * @var TrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        TrackRepositoryInterface $trackRepository,
        LoggerInterface $logger
    ) {
        $this->trackRepository = $trackRepository;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $trackId = (int) $this->getRequest()->getParam('track_id');
        try {
            $track = $this->trackRepository->get($trackId);
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }

        try {
            $this->trackRepository->delete($track);
            $redirectParams = ['order_id' => $track->getOrderId()];
            $message = __('Return shipment %1 was successfully deleted.', $track->getTrackNumber());
            $this->messageManager->addSuccessMessage($message);
        } catch (CouldNotDeleteException $exception) {
            $redirectParams = ['order_id' => $track->getOrderId(), 'active_tab' => 'nrshipping_order_returns'];
            $message = __('Return shipment %1 could not be deleted.', $track->getTrackNumber());
            $this->logger->error($message, ['exception' => $exception]);
            $this->messageManager->addErrorMessage($message);
        }

        $resultRedirect->setPath('sales/order/view', $redirectParams);
        return $resultRedirect;
    }
}
