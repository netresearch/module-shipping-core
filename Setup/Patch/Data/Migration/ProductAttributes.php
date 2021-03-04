<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data\Migration;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Setup\EavSetup;

class ProductAttributes
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

    /**
     * @param string[] $codeMap
     * @throws \Exception
     */
    public function migrate(array $codeMap): void
    {
        foreach ($codeMap as $oldAttributeCode => $newAttributeCode) {
            if (!$this->eavSetup->getAttribute(Product::ENTITY, $oldAttributeCode)) {
                continue;
            }

            $productTypes = [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE, Configurable::TYPE_CODE];

            $this->productCollection
                ->addAttributeToSelect([$oldAttributeCode])
                ->addFieldToFilter('type_id', ['in' => $productTypes]);

            /** @var Product $product */
            foreach ($this->productCollection as $product) {
                $product->addData([$newAttributeCode => $product->getData($oldAttributeCode)]);
                $this->productResource->saveAttribute($product, $newAttributeCode);
            }

            $this->eavSetup->removeAttribute(Product::ENTITY, $oldAttributeCode);
        }
    }
}
