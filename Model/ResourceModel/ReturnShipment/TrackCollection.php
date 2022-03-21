<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netresearch\ShippingCore\Model\ReturnShipment\Track as ReturnShipmentTrack;

/**
 * @method ReturnShipmentTrack[] getItems()
 */
class TrackCollection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(ReturnShipmentTrack::class, Track::class);
    }

    private function joinOrder()
    {
        $this->getSelect()
             ->join(
                 ['order' => $this->getTable('sales_order')],
                 'main_table.order_id = order.entity_id',
                 [
                     'order_increment_id' => 'order.increment_id'
                 ]
             );
    }

    public function setCustomerIdFilter(int $customerId)
    {
        $this->joinOrder();
        $this->addFieldToFilter('customer_id', ['eq' => $customerId]);
    }
}
