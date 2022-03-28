<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Rma;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CurrentTrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;

/**
 * Display return shipment labels.
 */
class View extends ReturnAction implements HttpGetActionInterface
{
    /**
     * @var OrderProviderInterface
     */
    private $orderProvider;

    /**
     * @var TrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var CurrentTrackInterface
     */
    private $trackProvider;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        OrderViewAuthorizationInterface $orderAuthorization,
        OrderProviderInterface $orderProvider,
        CanCreateReturnInterface $canCreateReturn,
        TrackRepositoryInterface $trackRepository,
        CurrentTrackInterface $trackProvider
    ) {
        $this->orderProvider = $orderProvider;
        $this->trackRepository = $trackRepository;
        $this->trackProvider = $trackProvider;

        parent::__construct($context, $orderRepository, $orderAuthorization, $orderProvider, $canCreateReturn);
    }

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $order = $this->orderProvider->getOrder();
        $orderNumber = $order->getRealOrderId();

        $trackId = (int) $this->getRequest()->getParam('track_id');
        $track = $this->trackRepository->get($trackId);
        $this->trackProvider->set($track);

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Order # %1', $orderNumber));
        $resultPage->getConfig()->getTitle()->prepend(__('Return Labels'));
        return $resultPage;
    }
}
