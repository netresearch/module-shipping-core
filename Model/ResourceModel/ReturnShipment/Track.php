<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Netresearch\ShippingCore\Setup\Module\Constants;

class Track extends AbstractDb
{
    /**
     * Init main table and primary key.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Constants::TABLE_RETURN_SHIPMENT_TRACK,
            \Netresearch\ShippingCore\Model\ReturnShipment\Track::ENTITY_ID
        );
    }
}
