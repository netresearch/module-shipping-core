<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\Quote\Address;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Netresearch\ShippingCore\Setup\Module\Constants;

class ShippingOptionSelection extends AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(Constants::TABLE_QUOTE_SHIPPING_OPTION_SELECTION, 'entity_id');
    }
}
