<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Adminhtml\Order\Rma;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;

class NewAction extends ReturnAction implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Magento_Sales::sales_order';

    public function execute(): ResultInterface
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Sales::sales_order');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Return'));

        return $this->_view->getPage();
    }
}
