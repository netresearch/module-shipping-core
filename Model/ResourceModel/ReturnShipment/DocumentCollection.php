<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Model\ReturnShipment\Document as ReturnShipmentDocument;

/**
 * @method ReturnShipmentDocument[] getItems()
 */
class DocumentCollection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(ReturnShipmentDocument::class, Document::class);
    }

    public function setTrackIdFilter(int $trackId)
    {
        $this->addFieldToFilter(DocumentInterface::TRACK_ID, ['eq' => $trackId]);
    }
}
