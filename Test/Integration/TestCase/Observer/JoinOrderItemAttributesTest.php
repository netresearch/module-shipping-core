<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Integration\TestCase\Observer;

use Magento\Sales\Api\Data\OrderItemExtension;
use Magento\Sales\Api\Data\OrderItemExtensionInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Sales\OrderBuilder;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

/**
 * Load order items with additional extension attributes.
 *
 * There are several ways to add an `item_id` filter. Make sure the field
 * is mapped properly to avoid ambiguous column errors.
 *
 * @see \Netresearch\ShippingCore\Plugin\Order\AddItemIdFilterMapping
 */
class JoinOrderItemAttributesTest extends TestCase
{
    /**
     * @var Order
     */
    private static $order;

    /**
     * @var string
     */
    private static $countryCode = 'CN';

    /**
     * @throws \Exception
     */
    public static function createOrder()
    {
        self::$order = OrderBuilder::anOrder()
            ->withShippingMethod('flatrate_flatrate')
            ->withProducts(
                ProductBuilder::aSimpleProduct()->withData(['country_of_manufacture' => self::$countryCode])
            )
            ->build();
    }

    /**
     * @throws \Exception
     */
    public static function createOrderRollback()
    {
        try {
            OrderFixtureRollback::create()->execute(new OrderFixture(self::$order));
        } catch (\Exception $exception) {
            $argv = $_SERVER['argv'] ?? [];
            if (in_array('--verbose', $argv, true)) {
                $message = sprintf("Error during rollback: %s%s", $exception->getMessage(), PHP_EOL);
                register_shutdown_function('fwrite', STDERR, $message);
            }
        }
    }

    /**
     * @test
     * @magentoDataFixture createOrder
     *
     * @magentoConfigFixture default_store shipping/origin/country_id DE
     * @magentoConfigFixture default_store shipping/origin/region_id 91
     * @magentoConfigFixture default_store shipping/origin/postcode 04229
     * @magentoConfigFixture default_store shipping/origin/city Leipzig
     * @magentoConfigFixture default_store shipping/origin/street_line1 Nonnenstraße 11
     */
    public function loadOrderItems()
    {
        $itemIds = array_map(
            function (Item $orderItem) {
                return (int) $orderItem->getId();
            },
            self::$order->getAllVisibleItems()
        );

        $filters = [
            function (Collection $collection) use ($itemIds) {
                $collection->setOrderFilter(self::$order);
                $collection->addIdFilter($itemIds);
            },
            function (Collection $collection) use ($itemIds) {
                $collection->setOrderFilter(self::$order);
                $collection->addFieldToFilter('item_id', ['in' => $itemIds]);
            },
            function (Collection $collection) use ($itemIds) {
                $collection->setOrderFilter(self::$order);
                $collection->addFieldToFilter(['item_id'], [['in' => $itemIds]]);
            },
        ];

        foreach ($filters as $filter) {
            /** @var Collection $orderItemCollection */
            $orderItemCollection = Bootstrap::getObjectManager()->create(Collection::class);
            $filter($orderItemCollection);

            /** @var OrderItemExtensionInterface|OrderItemExtension $orderItemExtAttrs */
            $orderItemExtAttrs = $orderItemCollection->getFirstItem()->getExtensionAttributes();
            self::assertInstanceOf(OrderItemExtensionInterface::class, $orderItemExtAttrs);
            self::assertSame(self::$countryCode, $orderItemExtAttrs->getNrshippingCountryOfManufacture());
        }
    }
}
