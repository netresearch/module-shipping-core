<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Netresearch\ShippingCore\Setup\Patch\Data\Migration\SalesTotals;

class MigrateSalesTotalsPatch implements DataPatchInterface
{
    /**
     * @var SalesTotals
     */
    private $totals;

    public function __construct(SalesTotals $totals)
    {
        $this->totals = $totals;
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
     * Migrate quote, order, invoice, credit memo additional fee totals from the dhl/shipping-m2 extension.
     *
     * @return void
     * @throws \Exception
     */
    public function apply()
    {
        $this->totals->migrate();
    }
}
