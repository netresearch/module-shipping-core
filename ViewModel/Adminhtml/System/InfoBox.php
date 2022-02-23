<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\ViewModel\Adminhtml\System;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Netresearch\ShippingCore\Api\InfoBox\VersionInterface;

class InfoBox implements ArgumentInterface
{
    /**
     * @var VersionInterface
     */
    private $version;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(VersionInterface $version, UrlInterface $urlBuilder)
    {
        $this->version = $version;
        $this->urlBuilder = $urlBuilder;
    }

    public function getModuleVersion(): string
    {
        return $this->version->getModuleVersion();
    }

    public function getParcelProcessingConfigUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'adminhtml/system_config/edit',
            [
                'section' => 'shipping',
                '_fragment' => 'shipping_parcel_processing-link',
            ]
        );
    }

    public function getBatchProcessingConfigUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'adminhtml/system_config/edit',
            [
                'section' => 'shipping',
                '_fragment' => 'shipping_batch_processing-link',
            ]
        );
    }
}
