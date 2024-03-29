<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Rma\View;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Helper\Guest as GuestHelper;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CurrentTrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;
use Netresearch\ShippingCore\Controller\Rma\View;

/**
 * Display return shipment labels for guests.
 */
class Guest extends View
{
    /**
     * @var GuestHelper
     */
    private $guestHelper;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        OrderViewAuthorizationInterface $orderAuthorization,
        OrderProviderInterface $orderProvider,
        CanCreateReturnInterface $canCreateReturn,
        TrackRepositoryInterface $trackRepository,
        CurrentTrackInterface $trackProvider,
        GuestHelper $guestHelper
    ) {
        $this->guestHelper = $guestHelper;

        parent::__construct(
            $context,
            $orderRepository,
            $orderAuthorization,
            $orderProvider,
            $canCreateReturn,
            $trackRepository,
            $trackProvider
        );
    }

    /**
     * Dispatch request. If loading the order fails, do not dispatch.
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        try {
            $this->guestHelper->loadValidOrder($request);
        } catch (LocalizedException $exception) {
            return $this->_redirect('sales/order/history');
        }

        return parent::dispatch($request);
    }
}
