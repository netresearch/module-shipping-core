<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class BatchProcessingConfig
{
    private const CONFIG_PATH_RETRY_FAILED = 'shipping/batch_processing/retry_failed_shipments';
    private const CONFIG_PATH_NOTIFY_CUSTOMER = 'shipping/batch_processing/autocreate_notify';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
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
