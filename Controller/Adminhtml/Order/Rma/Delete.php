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
    public const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

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

    #[\Override]
    public function execute(): ResultInterface
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
            $message = __('Return shipment %1 was successfully deleted.', $track->getTrackNumber());
            $this->messageManager->addSuccessMessage($message);
        } catch (CouldNotDeleteException $exception) {
            $message = __('Return shipment %1 could not be deleted.', $track->getTrackNumber());
            $this->logger->error($message, ['exception' => $exception]);
            $this->messageManager->addErrorMessage($message);
        }

        $resultRedirect->setPath('sales/order/view', ['order_id' => $track->getOrderId()]);
        return $resultRedirect;
    }
}
