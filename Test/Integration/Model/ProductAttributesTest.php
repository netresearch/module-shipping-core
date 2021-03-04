<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Integration\Model;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Netresearch\ShippingCore\Setup\Module\DataInstaller;
use PHPUnit\Framework\TestCase;

class ProductAttributesTest extends TestCase
{
    public function testProductAttributesProperlyCreated()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var AttributeCollectionFactory $attributeCollectionFactory */
        $attributeCollectionFactory = $objectManager->get(AttributeCollectionFactory::class);
        $attributeCollection = $attributeCollectionFactory->create();

        $attributes = [
            DataInstaller::ATTRIBUTE_CODE_EXPORT_DESCRIPTION,
            DataInstaller::ATTRIBUTE_CODE_HS_CODE
        ];

        $attributeCollection->addFieldToFilter('attribute_code', ['in' => $attributes]);

        self::assertEquals(count($attributes), $attributeCollection->getSize());
    }
}
