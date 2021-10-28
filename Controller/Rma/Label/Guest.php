<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Rma\Label;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Helper\Guest as GuestHelper;
use Magento\Shipping\Model\Shipment\ReturnShipmentFactory;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Api\Util\LabelDataProviderInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;
use Netresearch\ShippingCore\Controller\Rma\Label;
use Netresearch\ShippingCore\Model\BulkShipment\ReturnShipmentManagement;
use Psr\Log\LoggerInterface;

/**
 * Request and display return shipment labels for guests.
 */
class Guest extends Label
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
        ReturnShipmentFactory $shipmentRequestFactory,
        ReturnShipmentManagement $returnShipmentManagement,
        LabelDataProviderInterface $labelDataProvider,
        LoggerInterface $logger,
        GuestHelper $guestHelper
    ) {
        $this->guestHelper = $guestHelper;

        parent::__construct(
            $context,
            $orderRepository,
            $orderAuthorization,
            $orderProvider,
            $canCreateReturn,
            $shipmentRequestFactory,
            $returnShipmentManagement,
            $labelDataProvider,
            $logger
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
