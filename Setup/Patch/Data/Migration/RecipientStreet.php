<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data\Migration;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Netresearch\ShippingCore\Setup\Module\Constants;

class RecipientStreet
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @throws \Exception
     */
    public function migrate(): void
    {
        $connection = $this->resourceConnection->getConnection(Constants::SALES_CONNECTION_NAME);
        $oldTable = $this->resourceConnection->getTableName('dhlgw_recipient_street');
        $newTable = $this->resourceConnection->getTableName(Constants::TABLE_RECIPIENT_STREET);

        if (!$connection->isTableExists($oldTable)) {
            return;
        }

        $select = $connection->select()->from($oldTable);
        $query = $connection->insertFromSelect($select, $newTable, [], AdapterInterface::INSERT_IGNORE);
        $connection->query($query);
    }
}
