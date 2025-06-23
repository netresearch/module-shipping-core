<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Netresearch\ShippingCore\Setup\Patch\Data\Migration\ServiceSelections;

class MigrateServiceSelectionsPatch implements DataPatchInterface
{
    /**
     * @var ServiceSelections
     */
    private $selections;

    public function __construct(ServiceSelections $selections)
    {
        $this->selections = $selections;
    }

    #[\Override]
    public static function getDependencies(): array
    {
        return [];
    }

    #[\Override]
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Migrate shipping option and input codes from the dhl/shipping-m2 extension.
     *
     * @return void
     * @throws \Exception
     */
    #[\Override]
    public function apply()
    {
        $this->selections->migrate();
    }
}
