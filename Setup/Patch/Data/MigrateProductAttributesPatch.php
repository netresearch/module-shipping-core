<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Netresearch\ShippingCore\Setup\Module\DataInstaller;

class MigrateProductAttributesPatch implements DataPatchInterface
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @var Collection
     */
    private $productCollection;

    /**
     * @var ProductResource
     */
    private $productResource;

    public function __construct(EavSetup $eavSetup, Collection $productCollection, ProductResource $productResource)
    {
        $this->eavSetup = $eavSetup;
        $this->productCollection = $productCollection;
        $this->productResource = $productResource;
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
        if (!$this->eavSetup->getAttribute(Product::ENTITY, 'dhlgw_tariff_number')) {
            return;
        }

        $productTypes = [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE, Configurable::TYPE_CODE];

        $this->productCollection
            ->addAttributeToSelect(['dhlgw_tariff_number'])
            ->addFieldToFilter('type_id', ['in' => $productTypes]);

        /** @var Product $product */
        foreach ($this->productCollection as $product) {
            $product->addData([DataInstaller::ATTRIBUTE_CODE_HS_CODE => $product->getData('dhlgw_tariff_number')]);
            $this->productResource->saveAttribute($product, DataInstaller::ATTRIBUTE_CODE_HS_CODE);
        }
    }
}
