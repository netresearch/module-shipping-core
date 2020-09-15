<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\LabelStatus;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netresearch\ShippingCore\Model\LabelStatus\LabelStatus;
use Netresearch\ShippingCore\Model\ResourceModel\LabelStatus as LabelStatusResource;

class Collection extends AbstractCollection
{
    /**
     * Initialization
     */
    public function _construct()
    {
        $this->_init(LabelStatus::class, LabelStatusResource::class);
    }

    /**
     * Obtain the collection's status code column with order id as index.
     *
     * @return string[]
     */
    public function getValues(): array
    {
        return $this->_toOptionHash(LabelStatus::ORDER_ID, LabelStatus::STATUS_CODE);
    }
}
