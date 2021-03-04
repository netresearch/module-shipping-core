<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Netresearch\ShippingCore\Model\Config\BatchProcessingConfig;
use Netresearch\ShippingCore\Model\Config\ParcelProcessingConfig;
use Netresearch\ShippingCore\Setup\Patch\Data\Migration\Config;

class MigrateConfigPatch implements DataPatchInterface
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * Migrate config values from netresearch/module-shipping-core version 1.
     *
     * phpcs:disable Generic.Files.LineLength.TooLong
     *
     * @return void
     * @throws \Exception
     */
    public function apply()
    {
        $this->config->migrate([
            'shipping/batch_processing/cron_enabled' => BatchProcessingConfig::CONFIG_PATH_CRON_ENABLED,
            'shipping/batch_processing/cron_order_status' => BatchProcessingConfig::CONFIG_PATH_CRON_ORDER_STATUS,
            'shipping/batch_processing/retry_failed_shipments' => BatchProcessingConfig::CONFIG_PATH_RETRY_FAILED,
            'shipping/batch_processing/autocreate_notify' => BatchProcessingConfig::CONFIG_PATH_NOTIFY_CUSTOMER,
        ]);
    }
}
