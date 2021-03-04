<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Netresearch\ShippingCore\Setup\Module\DataInstaller;
use Netresearch\ShippingCore\Setup\Patch\Data\Migration\ProductAttributes;

class MigrateProductAttributesPatch implements DataPatchInterface
{
    /**
     * @var ProductAttributes
     */
    private $productAttributes;

    public function __construct(ProductAttributes $productAttributes)
    {
        $this->productAttributes = $productAttributes;
    }

    public static function getDependencies(): array
    {
        return [CreateProductAttributesPatch::class];
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * Migrate product attribute values from the dhl/shipping-m2 extension.
     *
     * @return void
     * @throws \Exception
     */
    public function apply()
    {
        $this->productAttributes->migrate([
            'dhlgw_tariff_number' => DataInstaller::ATTRIBUTE_CODE_HS_CODE,
            'dhlgw_export_description' => DataInstaller::ATTRIBUTE_CODE_EXPORT_DESCRIPTION,
        ]);
    }
}
