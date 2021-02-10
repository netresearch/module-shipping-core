<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Adminhtml\Order\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Netresearch\ShippingCore\Model\PackagingPopup\RequestDataConverter;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Sales::shipment';

    /**
     * @var RequestDataConverter
     */
    private $dataConverter;

    public function __construct(
        Context $context,
        RequestDataConverter $dataConverter
    ) {
        parent::__construct($context);

        $this->dataConverter = $dataConverter;
    }

    /**
     * Prepare the request data and forward it to the Magento_Shipping controller.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $json = $this->getRequest()->getParam('data');
        $requestData = $this->dataConverter->getData($json);
        $requestParams = [
            'shipment' => [
                'comment_text' => $requestData->getShipmentComment(),
                'send_email' => $requestData->isShipmentNotificationEnabled(),
                'comment_customer_notify' => $requestData->isCommentNotificationEnabled(),
                'create_shipping_label' => '1',
                'items' => $requestData->getShipmentItems(),
            ],
            'packages' => $requestData->getPackages()
        ];

        /** @var Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->setController('order_shipment')
                      ->setModule('admin')
                      ->setParams($requestParams);

        if ($this->getRequest()->getParam('shipment_id', false)) {
            $resultForward->forward('createLabel');
        } else {
            $resultForward->forward('save');
        }

        return $resultForward;
    }
}
