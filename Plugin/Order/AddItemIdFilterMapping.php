<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\Order;

use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection;

/**
 * Add field mapping for the order item collection's primary key.
 *
 * When the order item collection is loaded, then the shipping module adds some
 * extension attributes. The database table that holds the additional attributes
 * also has a field `item_id` (both primary and foreign key constraint). This
 * leads to an integrity constraint/ambiguous column error when the `item_id`
 * filter is set with no table alias. To fix this, we add the filter mapping.
 *
 * @see \Magento\Sales\Model\ResourceModel\Order\Item\Collection::addIdFilter
 * @see \Magento\Framework\Data\Collection\AbstractDb::addFieldToFilter
 * @see \Netresearch\ShippingCore\Observer\JoinOrderItemAttributes
 */
class AddItemIdFilterMapping
{
    /**
     * @param Collection $collection
     * @param mixed $field
     */
    public function beforeAddFieldToFilter(Collection $collection, $field): void
    {
        $idField = OrderItemInterface::ITEM_ID;

        if (($field === $idField) || (is_array($field) && in_array($idField, $field))) {
            $collection->addFilterToMap($idField, "main_table.$idField");
        }
    }
}
