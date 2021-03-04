<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data\Migration;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Netresearch\ShippingCore\Setup\Module\Constants;

class ServiceSelections
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    private function migrateData(string $connectionName, string $oldTable, string $newTable): void
    {
        $connection = $this->resourceConnection->getConnection($connectionName);
        $oldTable = $connection->getTableName($oldTable);
        $newTable = $connection->getTableName($newTable);

        if (!$connection->isTableExists($oldTable)) {
            return;
        }

        $select = $connection->select()->from(
            $oldTable,
            ['parent_id', 'shipping_option_code', 'input_code', 'input_value']
        );

        $query = $connection->insertFromSelect(
            $select,
            $newTable,
            [
                AssignedSelectionInterface::PARENT_ID,
                AssignedSelectionInterface::SHIPPING_OPTION_CODE,
                AssignedSelectionInterface::INPUT_CODE,
                AssignedSelectionInterface::INPUT_VALUE
            ],
            AdapterInterface::INSERT_IGNORE
        );
        $connection->query($query);
    }

    private function migrateCodes(
        string $connectionName,
        string $table,
        string $oldOptionCode,
        string $newOptionCode,
        array $inputCodes
    ): void {
        $connection = $this->resourceConnection->getConnection($connectionName);
        $tableName = $connection->getTableName($table);

        foreach ($inputCodes as $oldInputCode => $newInputCode) {
            $connection->update(
                $tableName,
                [AssignedSelectionInterface::INPUT_CODE => $newInputCode],
                [
                    AssignedSelectionInterface::INPUT_CODE . ' = ?' => $oldInputCode,
                    AssignedSelectionInterface::SHIPPING_OPTION_CODE . ' = ?' => $oldOptionCode,
                ]
            );
        }

        $connection->update(
            $tableName,
            [AssignedSelectionInterface::SHIPPING_OPTION_CODE => $newOptionCode],
            [
                AssignedSelectionInterface::SHIPPING_OPTION_CODE . ' = ?' => $oldOptionCode,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function migrate(): void
    {
        $connections = [
            Constants::CHECKOUT_CONNECTION_NAME => [
                'dhlgw_quote_address_shipping_option_selection' => Constants::TABLE_QUOTE_SHIPPING_OPTION_SELECTION,
            ],
            Constants::SALES_CONNECTION_NAME => [
                'dhlgw_order_address_shipping_option_selection' => Constants::TABLE_ORDER_SHIPPING_OPTION_SELECTION,
            ]
        ];

        foreach ($connections as $connectionName => $tables) {
            foreach ($tables as $oldTable => $newTable) {
                $this->migrateData($connectionName, $oldTable, $newTable);
                $this->migrateCodes(
                    $connectionName,
                    $newTable,
                    'parcelshopFinder',
                    Codes::SERVICE_OPTION_DELIVERY_LOCATION,
                    [
                        'locationType' => Codes::SERVICE_INPUT_DELIVERY_LOCATION_TYPE,
                        'locationNumber' => Codes::SERVICE_INPUT_DELIVERY_LOCATION_NUMBER,
                        'locationId' => Codes::SERVICE_INPUT_DELIVERY_LOCATION_ID,
                    ]
                );
            }
        }
    }
}
