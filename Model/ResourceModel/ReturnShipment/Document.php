<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Netresearch\ShippingCore\Setup\Module\Constants;

class Document extends AbstractDb
{
    /**
     * Init main table and primary key.
     *
     * @return void
     */
    #[\Override]
    protected function _construct()
    {
        $this->_init(
            Constants::TABLE_RETURN_SHIPMENT_DOCUMENT,
            \Netresearch\ShippingCore\Model\ReturnShipment\Document::ENTITY_ID
        );
    }
}
