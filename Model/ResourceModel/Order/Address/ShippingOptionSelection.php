<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\Order\Address;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netresearch\ShippingCore\Setup\Module\Constants;

class ShippingOptionSelection extends AbstractDb
{
    /**
     * Resource initialization
     */
    #[\Override]
    protected function _construct()
    {
        $this->_init(Constants::TABLE_ORDER_SHIPPING_OPTION_SELECTION, 'entity_id');
    }
}
