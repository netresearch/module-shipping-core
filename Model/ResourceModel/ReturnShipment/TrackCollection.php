<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Model\ReturnShipment\Track as ReturnShipmentTrack;

/**
 * @method ReturnShipmentTrack[] getItems()
 */
class TrackCollection extends AbstractCollection
{
    #[\Override]
    protected function _construct()
    {
        $this->_init(ReturnShipmentTrack::class, Track::class);
    }

    private function joinOrder(): void
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

    public function setOrderIdFilter(int $orderId): void
    {
        $this->addFieldToFilter('order_id', ['eq' => $orderId]);
    }

    public function setCustomerIdFilter(int $customerId): void
    {
        $this->joinOrder();
        $this->addFieldToFilter('customer_id', ['eq' => $customerId]);
    }

    public function setTrackingNumberFilter(string $trackingNumber): void
    {
        $this->addFieldToFilter(TrackInterface::TRACK_NUMBER, ['eq' => $trackingNumber]);
    }

    #[\Override]
    protected function _afterLoad(): void
    {
        parent::_afterLoad();

        foreach ($this->_items as $item) {
            if ($item instanceof AbstractModel) {
                $this->getResource()->afterLoad($item);
            }
        }
    }
}
