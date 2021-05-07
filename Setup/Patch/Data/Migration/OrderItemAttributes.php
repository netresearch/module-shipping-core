<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data\Migration;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Netresearch\ShippingCore\Setup\Module\Constants;

class OrderItemAttributes
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
     * @param string[] $columnMap
     * @throws \Exception
     */
    public function migrate(array $columnMap): void
    {
        $connection = $this->resourceConnection->getConnection(Constants::SALES_CONNECTION_NAME);
        $oldTable = $this->resourceConnection->getTableName('dhlgw_order_item');
        $newTable = $this->resourceConnection->getTableName(Constants::TABLE_ORDER_ITEM);

        if (!$connection->isTableExists($oldTable)) {
            return;
        }

        $select = $connection->select()->from($oldTable, array_keys($columnMap));
        $query = $connection->insertFromSelect(
            $select,
            $newTable,
            array_values($columnMap),
            AdapterInterface::INSERT_IGNORE
        );
        $connection->query($query);
    }
}
