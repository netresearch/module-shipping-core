<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\ViewModel\Adminhtml\System;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Netresearch\ShippingCore\Api\InfoBox\VersionInterface;

class InfoBox implements ArgumentInterface
{
    /**
     * @var VersionInterface
     */
    private $version;

    /**
     * InfoBox constructor.
     *
     * @param VersionInterface $version
     */
    public function __construct(VersionInterface $version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getModuleVersion(): string
    {
        return $this->version->getModuleVersion();
    }
}
