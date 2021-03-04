<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data\Migration;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Sales\Model\ResourceModel\GridInterface;
use Netresearch\ShippingCore\Model\AdditionalFee\TotalsManager;
use Netresearch\ShippingCore\Setup\Module\Constants;

class SalesTotals
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
        $tables = [
            'quote' => Constants::CHECKOUT_CONNECTION_NAME,
            'sales_order' => Constants::SALES_CONNECTION_NAME,
            'sales_invoice' => Constants::SALES_CONNECTION_NAME,
            'sales_creditmemo' => Constants::SALES_CONNECTION_NAME,
        ];

        $columns = [
            'base_dhlgw_additional_fee' => TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME,
            'base_dhlgw_additional_fee_incl_tax' => TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME,
            'dhlgw_additional_fee' => TotalsManager::ADDITIONAL_FEE_FIELD_NAME,
            'dhlgw_additional_fee_incl_tax' => TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME,
        ];

        foreach ($tables as $table => $connectionName) {
            $connection = $this->resourceConnection->getConnection($connectionName);
            $tableName = $connection->getTableName($table);
            if (!$connection->tableColumnExists($tableName, 'base_dhlgw_additional_fee')) {
                continue;
            }

            foreach ($columns as $oldColumn => $newColumn) {
                $connection->update(
                    $tableName,
                    [$newColumn => new \Zend_Db_Expr($oldColumn)],
                    "$oldColumn IS NOT NULL"
                );

            }
        }
    }
}
