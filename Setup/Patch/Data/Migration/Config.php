<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data\Migration;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @api
 */
class Config
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
     * @param string[] $configPathMap
     * @throws \Exception
     */
    public function migrate(array $configPathMap): void
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('core_config_data');

        foreach ($configPathMap as $oldPath => $newPath) {
            $cols = [
                'scope' => 'scope',
                'scope_id' => 'scope_id',
                'path' => new \Zend_Db_Expr("'$newPath'"),
                'value' => 'value'
            ];

            $select = $connection->select()->from($table, $cols)->where('path = ?', $oldPath);
            $query = $connection->insertFromSelect($select, $table, array_keys($cols), AdapterInterface::INSERT_IGNORE);
            $connection->query($query);
        }
    }
}
