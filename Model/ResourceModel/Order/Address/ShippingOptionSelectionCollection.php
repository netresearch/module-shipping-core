<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\Order\Address;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netresearch\ShippingCore\Model\ResourceModel\Order\Address\ShippingOptionSelection as ServiceSelectionResource;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\OrderSelection;

/**
 * @method OrderSelection[] getItems()
 */
class ShippingOptionSelectionCollection extends AbstractCollection
{
    #[\Override]
    protected function _construct()
    {
        $this->_init(OrderSelection::class, ServiceSelectionResource::class);
    }
}
