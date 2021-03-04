<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data\Migration;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Sales\Model\ResourceModel\GridInterface;
use Netresearch\ShippingCore\Setup\Module\Constants;

class LabelStatus
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var GridInterface
     */
    private $orderGrid;

    public function __construct(ResourceConnection $resourceConnection, GridInterface $orderGrid)
    {
        $this->resourceConnection = $resourceConnection;
        $this->orderGrid = $orderGrid;
    }

    /**
     * @throws \Exception
     */
    public function migrate(): void
    {
        $connection = $this->resourceConnection->getConnection(Constants::SALES_CONNECTION_NAME);
        $oldTable = $connection->getTableName('dhlgw_label_status');
        $newTable = $connection->getTableName(Constants::TABLE_LABEL_STATUS);

        if (!$connection->isTableExists($oldTable)) {
            return;
        }

        $select = $connection->select()->from($oldTable);
        $query = $connection->insertFromSelect($select, $newTable, [], AdapterInterface::INSERT_IGNORE);
        $connection->query($query);

        $this->orderGrid->refreshBySchedule();
    }
}
