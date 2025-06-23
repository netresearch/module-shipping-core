<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Rma;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;

/**
 * Return shipment form.
 */
class Create extends ReturnAction
{
    /**
     * @var OrderProviderInterface
     */
    private $orderProvider;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        OrderViewAuthorizationInterface $orderAuthorization,
        OrderProviderInterface $orderProvider,
        CanCreateReturnInterface $canCreateReturn
    ) {
        $this->orderProvider = $orderProvider;

        parent::__construct($context, $orderRepository, $orderAuthorization, $orderProvider, $canCreateReturn);
    }

    /**
     * Customer create new return.
     *
     * @return ResultInterface|ResponseInterface
     */
    #[\Override]
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Order # %1', $this->orderProvider->getOrder()->getRealOrderId()));
        $resultPage->getConfig()->getTitle()->prepend(__('New Return Shipment'));

        return $resultPage;
    }
}
