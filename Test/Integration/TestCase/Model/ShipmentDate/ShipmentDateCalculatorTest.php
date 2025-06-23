<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Integration\TestCase\Model\ShipmentDate;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Netresearch\ShippingCore\Model\ShipmentDate\ShipmentDateCalculator;
use Netresearch\ShippingCore\Model\ShipmentDate\Validator\ExcludeHolidays;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\OrderBuilder;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

class ShipmentDateCalculatorTest extends TestCase
{
    /**
     * @var Order
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
     * Roll back fixture.
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
     * Test data provider.
     *
     * @return \DateTime[][]|string[][]
     */
    public static function dataProvider(): array
    {
        // cut-off times, shipments created before that time are handed over to the carrier on the same day.
        $fri = new \DateTimeImmutable('2019-12-25 17:00:00');
        $mon = new \DateTimeImmutable('2019-12-27 15:00:00');
        $wed = new \DateTimeImmutable('2019-12-30 17:00:00');

        $cutOffTimes = [
            $fri->format('N') => $fri,
            $mon->format('N') => $mon,
            $wed->format('N') => $wed,
        ];

        return [
            // ship same day
            'before_cut_off_on_regular_day' => [
                $cutOffTimes,
                $wed->setTime(10, 0),
                $wed,
            ],
            // ship on next day
            'before_cut_off_on_holiday' => [
                $cutOffTimes,
                $fri->setTime(10, 0),
                $mon,
            ],
            // ship on next day
            'after_cut_off_on_regular_day' => [
                $cutOffTimes,
                $mon->setTime(17, 10),
                $wed,
            ],
            // skip one drop-off, ship on day after the next configured day
            'after_cut_off_upcoming_holiday' => [
                $cutOffTimes,
                $wed->modify('-1 weeks')->setTime(17, 10),
                $mon,
            ],
            // skip two drop-offs
            'before_cut_off_on_holiday_with_upcoming_holiday' => [
                [$fri->format('N') => $fri],
                $fri->setTime(10, 0),
                $fri->modify('+2 weeks'),
            ],
        ];
    }

    /**
     * Assert that the correct shipment date gets calculated.
     *
     * Calculation takes into account current time, cut-off time, and holidays.
     *
     * @magentoDataFixture createOrder
     *
     * @magentoConfigFixture default_store shipping/origin/country_id DE
     * @magentoConfigFixture default_store shipping/origin/region_id 91
     * @magentoConfigFixture default_store shipping/origin/postcode 04229
     * @magentoConfigFixture default_store shipping/origin/city Leipzig
     * @magentoConfigFixture default_store shipping/origin/street_line1 Nonnenstraße 11
     *
     * @param \DateTimeInterface[] $cutOffTimes
     * @param \DateTimeInterface $currentTime
     * @param \DateTimeInterface $expectedDate
     *
     * @throws \RuntimeException
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('dataProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function calculateShipmentDate(
        array $cutOffTimes,
        \DateTimeInterface $currentTime,
        \DateTimeInterface $expectedDate
    ) {
        $timezoneMock = $this->getMockBuilder(TimezoneInterface::class)->disableOriginalConstructor()->getMock();
        $timezoneMock->method('scopeDate')->with(self::anything(), null, true)->willReturn($currentTime);

        $dayValidator = Bootstrap::getObjectManager()->create(ExcludeHolidays::class);

        /** @var ShipmentDateCalculator $subject */
        $subject = Bootstrap::getObjectManager()->create(
            ShipmentDateCalculator::class,
            [
                'timezone' => $timezoneMock,
                'dayValidators' => [$dayValidator],
            ]
        );

        $shipmentDate = $subject->getDate($cutOffTimes, self::$order->getStoreId());

        self::assertEquals($expectedDate, $shipmentDate);
    }

    /**
     * Assert behaviour when no shipment date can be calculated.
     *
     * Invalid drop-off configuration might lead to no shipment date.
     * Make sure an exception is thrown to indicate an error.
     *
     * @magentoConfigFixture default_store shipping/origin/country_id DE
     * @magentoConfigFixture default_store shipping/origin/region_id 91
     * @magentoConfigFixture default_store shipping/origin/postcode 04229
     * @magentoConfigFixture default_store shipping/origin/city Leipzig
     * @magentoConfigFixture default_store shipping/origin/street_line1 Nonnenstraße 11
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function calculationError()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No applicable shipment date found.');

        $fri = new \DateTimeImmutable('2019-12-25 17:00:00');
        $currentTime = $fri->setTime(10, 0);
        $cutOffTimes = [
            $fri->format('N') => $fri,
        ];

        $timezoneMock = $this->getMockBuilder(TimezoneInterface::class)->disableOriginalConstructor()->getMock();
        $timezoneMock->method('scopeDate')->with(self::anything(), null, true)->willReturn($currentTime);

        // every day is a holiday!
        $dayValidatorMock = $this->getMockBuilder(ExcludeHolidays::class)->disableOriginalConstructor()->getMock();
        $dayValidatorMock->method('validate')->willReturn(false);

        /** @var ShipmentDateCalculator $subject */
        $subject = Bootstrap::getObjectManager()->create(
            ShipmentDateCalculator::class,
            [
                'timezone' => $timezoneMock,
                'dayValidators' => [$dayValidatorMock],
            ]
        );

        $subject->getDate($cutOffTimes, self::$order->getStoreId());
    }
}
