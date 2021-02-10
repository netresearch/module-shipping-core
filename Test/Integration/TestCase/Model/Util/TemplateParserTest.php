<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Integration\TestCase\Model\Util;

use Netresearch\ShippingCore\Model\Util\TemplateParser;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\OrderBuilder;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

class TemplateParserTest extends TestCase
{
    /**
     * @var OrderInterface
     */
    private static $order;

    /**
     * @throws \Exception
     */
    public static function createOrder()
    {
        self::$order = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate')->build();
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
     */
    public function parse()
    {
        $entityId = self::$order->getEntityId();
        $incrementId = self::$order->getIncrementId();
        $firstName = self::$order->getBillingAddress()->getFirstname();
        $lastName = self::$order->getBillingAddress()->getLastname();

        $template = 'Order #{{increment_id}} ({{entity_id}}) for {{firstname}} {{lastname}} {{foo}}.';
        $expected = "Order #$incrementId ($entityId) for $firstName $lastName {{foo}}.";

        /** @var TemplateParser $parser */
        $parser = Bootstrap::getObjectManager()->get(TemplateParser::class);
        self::assertSame($expected, $parser->parse(self::$order, $template));
    }
}
