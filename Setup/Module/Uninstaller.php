<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Module;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Module\Setup;
use Magento\Framework\Setup\SchemaSetupInterface;

class Uninstaller
{
    /**
     * Delete all config data related to Netresearch_ShippingCore.
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     */
    public static function deleteConfig(SchemaSetupInterface $schemaSetup)
    {
        $defaultConnection = $schemaSetup->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $configTable = $schemaSetup->getTable('core_config_data', ResourceConnection::DEFAULT_CONNECTION);
        $defaultConnection->delete($configTable, "`path` LIKE 'shipping/parcel_processing/%'");
        $defaultConnection->delete($configTable, "`path` LIKE 'shipping/batch_processing/%'");
    }

    /**
     * @param EavSetup $eavSetup
     */
    public static function deleteAttributes(EavSetup $eavSetup)
    {
        $eavSetup->removeAttribute(Product::ENTITY, DataInstaller::ATTRIBUTE_CODE_HS_CODE);
    }
}
