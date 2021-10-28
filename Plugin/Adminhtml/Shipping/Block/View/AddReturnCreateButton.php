<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\Adminhtml\Shipping\Block\View;

use Magento\Sales\Block\Adminhtml\Order\View;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;

class AddReturnCreateButton
{
    /**
     * @var CanCreateReturnInterface
     */
    private $canCreateReturn;

    /**
     * @param CanCreateReturnInterface $canCreateReturn
     */
    public function __construct(CanCreateReturnInterface $canCreateReturn)
    {
        $this->canCreateReturn = $canCreateReturn;
    }

    /**
     * Add "Create Returns" button if it is not provided by the Magento_Rma module.
     *
     * @param View $view
     * @return null
     */
    public function beforeSetLayout(View $view)
    {
        if ($this->canCreateReturn->execute($view->getOrder())) {
            $url = $view->getUrl('nrshipping/order_rma/new', ['order_id' => $view->getOrderId()]);
            $view->addButton(
                'nrshipping_order_return',
                [
                    'label' => __('Create Returns'),
                    'onclick' => "setLocation('$url')"
                ]
            );
        }

        return null;
    }
}
