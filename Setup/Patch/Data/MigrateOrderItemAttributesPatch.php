<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Netresearch\ShippingCore\Setup\Module\DataInstaller;
use Netresearch\ShippingCore\Setup\Patch\Data\Migration\OrderItemAttributes;

class MigrateOrderItemAttributesPatch implements DataPatchInterface
{
    /**
     * @var OrderItemAttributes
     */
    private $itemAttributes;

    public function __construct(OrderItemAttributes $itemAttributes)
    {
        $this->itemAttributes = $itemAttributes;
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
     * Migrate order item attribute values from the dhl/shipping-m2 extension.
     *
     * @return void
     * @throws \Exception
     */
    public function apply()
    {
        $this->itemAttributes->migrate([
            'item_id' => 'item_id',
            'country_of_manufacture' => 'country_of_manufacture',
            'export_description' => 'export_description',
            'tariff_number' => 'hs_code'
        ]);
    }
}
