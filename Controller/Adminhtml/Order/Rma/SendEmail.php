<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Adminhtml\Order\Rma;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CurrentTrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;
use Netresearch\ShippingCore\Model\Email\ReturnShipment\TransportBuilder;

class SendEmail extends Action
{
    public const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var TrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CurrentTrackInterface
     */
    private $trackProvider;

    /**
     * @var OrderProviderInterface
     */
    private $orderProvider;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        TrackRepositoryInterface $trackRepository,
        OrderRepositoryInterface $orderRepository,
        CurrentTrackInterface $trackProvider,
        OrderProviderInterface $orderProvider
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->trackRepository = $trackRepository;
        $this->orderRepository = $orderRepository;
        $this->trackProvider = $trackProvider;
        $this->orderProvider = $orderProvider;

        parent::__construct($context);
    }

    #[\Override]
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $trackId = (int) $this->getRequest()->getParam('track_id');

        try {
            $track = $this->trackRepository->get($trackId);
            $this->trackProvider->set($track);

            $order = $this->orderRepository->get($track->getOrderId());
            $this->orderProvider->setOrder($order);

            $transport = $this->transportBuilder
                ->setTrack($track)
                ->setOrder($order)
                ->build();
            $transport->sendMessage();

            $this->messageManager->addSuccessMessage(__('Return shipment labels were sent to the customer.'));
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }

        $resultRedirect->setPath('sales/order/view', ['order_id' => $track->getOrderId()]);
        return $resultRedirect;
    }
}
