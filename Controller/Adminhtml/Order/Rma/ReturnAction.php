<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Adminhtml\Order\Rma;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderRepository;
use Netresearch\ShippingCore\Api\Util\OrderProviderInterface;

/**
 * @method \Magento\Framework\App\Request\Http getRequest()
 */
abstract class ReturnAction extends Action
{
    /**
     * @var OrderRepositoryInterface|OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderProviderInterface
     */
    private $orderProvider;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        OrderProviderInterface $orderProvider
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderProvider = $orderProvider;

        parent::__construct($context);
    }

    /**
     * @param RequestInterface $request
     * @return ResultInterface|ResponseInterface
     */
    #[\Override]
    public function dispatch(RequestInterface $request)
    {
        $orderId = (int) $this->getRequest()->getParam('order_id');

        try {
            $order = $this->orderRepository->get($orderId);
            $this->orderProvider->setOrder($order);
        } catch (LocalizedException) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));

            $this->_actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
            $this->_redirect('sales/order/index');
        }

        return parent::dispatch($request);
    }
}
