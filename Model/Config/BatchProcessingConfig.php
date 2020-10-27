<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoresConfig;

class BatchProcessingConfig
{
    private const CONFIG_PATH_CRON_ENABLED = 'shipping/batch_processing/cron_enabled';
    private const CONFIG_PATH_CRON_ORDER_STATUS = 'shipping/batch_processing/cron_order_status';
    private const CONFIG_PATH_RETRY_FAILED = 'shipping/batch_processing/retry_failed_shipments';
    private const CONFIG_PATH_NOTIFY_CUSTOMER = 'shipping/batch_processing/autocreate_notify';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoresConfig
     */
    private $storesConfig;

    public function __construct(ScopeConfigInterface $scopeConfig, StoresConfig $storesConfig)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storesConfig = $storesConfig;
    }

    /**
     * Obtain the stores which are enabled for cron auto-create.
     *
     * @return int[]
     */
    public function getAutoCreateStores(): array
    {
        $storesConfig = $this->storesConfig->getStoresConfigByPath(self::CONFIG_PATH_CRON_ENABLED);
        $activeStores = array_filter($storesConfig);

        return array_keys($activeStores);
    }

    /**
     * Get allowed order statuses for cron auto-create
     *
     * @return string Comma-separated list of order status
     */
    public function getAutoCreateOrderStatus(): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_CRON_ORDER_STATUS);
    }

    /**
     * Check whether or not failed shipments should be automatically retried during bulk/cron processing.
     *
     * @return bool
     */
    public function isRetryEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_RETRY_FAILED);
    }

    /**
     * Check whether or not a shipment confirmation email should be sent after successful bulk/cron processing.
     *
     * @param mixed $store
     * @return bool
     */
    public function isNotificationEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_NOTIFY_CUSTOMER,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
