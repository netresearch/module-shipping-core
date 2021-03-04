<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Netresearch\ShippingCore\Setup\Patch\Data\Migration\LabelStatus;

class MigrateLabelStatusPatch implements DataPatchInterface
{
    /**
     * @var LabelStatus
     */
    private $labelStatus;

    public function __construct(LabelStatus $labelStatus)
    {
        $this->labelStatus = $labelStatus;
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
     * Migrate label status values from the dhl/shipping-m2 extension.
     *
     * @return void
     * @throws \Exception
     */
    public function apply()
    {
        $this->labelStatus->migrate();
    }
}
