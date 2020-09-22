<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\RecipientStreet;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netresearch\ShippingCore\Model\ResourceModel\RecipientStreet as RecipientStreetResource;
use Netresearch\ShippingCore\Model\SplitAddress\RecipientStreet;

class Collection extends AbstractCollection
{
    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_init(RecipientStreet::class, RecipientStreetResource::class);
    }
}
