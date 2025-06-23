<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Integration\TestCase\Model\AdditionalFee;

use Magento\Quote\Api\Data\ShippingInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\ShippingAssignment;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Netresearch\ShippingCore\Model\AdditionalFee\Total;
use Netresearch\ShippingCore\Model\AdditionalFee\TotalsManager;
use Netresearch\ShippingCore\Test\Integration\Fixture\Data\FakeAdditionalFeeConfiguration;
use Netresearch\ShippingCore\Test\Integration\Fixture\Data\FakeAdditionalFeeManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TotalTest extends TestCase
{
    const TEST_SHIPPING_METHOD_CODE = 'test';

    /**
     * @var ObjectManager
     */
    private $objectManger;

    /**
     * @var ShippingAssignment|MockObject
     */
    private $mockShippingAssignment;

    /**
     * @var Quote
     */
    private $testQuote;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManger = Bootstrap::getObjectManager();

        /** @var Quote\Address $testAddress */
        $testAddress = $this->objectManger->create(Quote\Address::class);
        $testAddress->setAddressType(Address::ADDRESS_TYPE_SHIPPING);
        $testAddress->setShippingMethod(FakeAdditionalFeeConfiguration::CARRIERCODE . '_123');
        $testAddress->setId('testAddressId');

        $mockShipping = $this->getMockBuilder(ShippingInterface::class)->getMock();
        $mockShipping->method('getAddress')->willReturn($testAddress);
        $this->mockShippingAssignment = $this->getMockBuilder(ShippingAssignment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockShippingAssignment->method('getShipping')->willReturn($mockShipping);

        /** @var Quote $testQuote */
        $this->testQuote = $this->objectManger->create(Quote::class);
        $this->testQuote->setShippingMethod(FakeAdditionalFeeConfiguration::CARRIERCODE . '_123');
        $this->testQuote->setShippingAddress($testAddress);
    }

    public function testGetLabel()
    {
        /** @var Total $subject */
        $subject = $this->objectManger->create(
            Total::class,
            ['additionalFeeManagement' => new FakeAdditionalFeeManagement()]
        );
        self::assertSame(
            FakeAdditionalFeeConfiguration::LABEL,
            $subject->getLabel(FakeAdditionalFeeConfiguration::CARRIERCODE)->render()
        );
    }

    /**
     * @return array
     */
    public static function getCurrencyData(): array
    {
        return [
            'same currencies' => [
                'baseCurrency' => 'USD',
                'quoteCurrency' => 'USD',
            ],
            'different currencies' => [
                'baseCurrency' => 'USD',
                'quoteCurrency' => 'EUR',
            ]
        ];
    }

    /**
     * @param string $baseCurrency
     * @param string $quoteCurrency
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getCurrencyData')]
    public function testCollect(string $baseCurrency, string $quoteCurrency)
    {
        /** @var Total $subject */
        $subject = $this->objectManger->create(
            Total::class,
            ['additionalFeeManagement' => new FakeAdditionalFeeManagement()]
        );
        /** @var Quote\Address\Total $testTotal */
        $testTotal = $this->objectManger->create(Quote\Address\Total::class);

        $this->testQuote->setBaseCurrencyCode($baseCurrency);
        $this->testQuote->setQuoteCurrencyCode($quoteCurrency);

        $subject->collect($this->testQuote, $this->mockShippingAssignment, $testTotal);

        /** Check base totals */
        self::assertSame(
            FakeAdditionalFeeConfiguration::CHARGE,
            $testTotal->getBaseTotalAmount(Total::SERVICE_CHARGE_TOTAL_CODE)
        );
        self::assertSame(
            FakeAdditionalFeeConfiguration::CHARGE,
            $this->testQuote->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME)
        );

        /** Check converted totals */
        if ($baseCurrency === $quoteCurrency) {
            self::assertSame(
                FakeAdditionalFeeConfiguration::CHARGE,
                $testTotal->getTotalAmount(Total::SERVICE_CHARGE_TOTAL_CODE)
            );
            self::assertSame(
                FakeAdditionalFeeConfiguration::CHARGE,
                $this->testQuote->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
            );
        } else {
            self::assertGreaterThan(
                0,
                $testTotal->getTotalAmount(Total::SERVICE_CHARGE_TOTAL_CODE)
            );
            self::assertGreaterThan(
                0,
                $this->testQuote->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
            );
            self::assertLessThan(
                FakeAdditionalFeeConfiguration::CHARGE,
                $testTotal->getTotalAmount(Total::SERVICE_CHARGE_TOTAL_CODE)
            );
            self::assertLessThan(
                FakeAdditionalFeeConfiguration::CHARGE,
                $this->testQuote->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
            );
        }
    }

    /**
     * @param string $baseCurrency
     * @param string $quoteCurrency
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getCurrencyData')]
    public function testFetch(string $baseCurrency, string $quoteCurrency)
    {
        /** @var Total $subject */
        $subject = $this->objectManger->create(
            Total::class,
            ['additionalFeeManagement' => new FakeAdditionalFeeManagement()]
        );
        /** @var Quote\Address\Total $testTotal */
        $testTotal = $this->objectManger->create(Quote\Address\Total::class);

        $this->testQuote->setBaseCurrencyCode($baseCurrency);
        $this->testQuote->setQuoteCurrencyCode($quoteCurrency);

        $result = $subject->fetch($this->testQuote, $testTotal);

        self::assertSame(Total::SERVICE_CHARGE_TOTAL_CODE, $result['code']);
        self::assertSame(FakeAdditionalFeeConfiguration::LABEL, (string)$result['title']);
        if ($baseCurrency === $quoteCurrency) {
            self::assertSame(FakeAdditionalFeeConfiguration::CHARGE, $result['value']);
        } else {
            self::assertGreaterThan(0, $result['value']);
            self::assertLessThan(FakeAdditionalFeeConfiguration::CHARGE, $result['value']);
        }
    }

    /**
     * @param string $baseCurrency
     * @param string $quoteCurrency
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getCurrencyData')]
    public function testCreateTotalDisplayObject(string $baseCurrency, string $quoteCurrency)
    {
        /** @var Total $subject */
        $subject = $this->objectManger->create(
            Total::class,
            ['additionalFeeManagement' => new FakeAdditionalFeeManagement()]
        );
        /** @var Quote\Address\Total $testTotal */

        $this->testQuote->setBaseCurrencyCode($baseCurrency);
        $this->testQuote->setQuoteCurrencyCode($quoteCurrency);
        $this->testQuote->setData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME, FakeAdditionalFeeConfiguration::CHARGE);
        if ($baseCurrency === $quoteCurrency) {
            $this->testQuote->setData(
                TotalsManager::ADDITIONAL_FEE_FIELD_NAME,
                FakeAdditionalFeeConfiguration::CHARGE
            );
        } else {
            $this->testQuote->setData(
                TotalsManager::ADDITIONAL_FEE_FIELD_NAME,
                FakeAdditionalFeeConfiguration::CHARGE / 2
            );
        }

        $result = $subject->createTotalDisplayObject($this->testQuote);

        self::assertSame(Total::SERVICE_CHARGE_TOTAL_CODE, $result->getCode());
        self::assertSame(FakeAdditionalFeeConfiguration::CHARGE, $result->getBaseValue());
        self::assertSame(FakeAdditionalFeeConfiguration::LABEL, (string)$result->getLabel());
        if ($baseCurrency === $quoteCurrency) {
            self::assertSame(FakeAdditionalFeeConfiguration::CHARGE, $result->getValue());
        } else {
            self::assertGreaterThan(0, $result->getValue());
            self::assertLessThan(FakeAdditionalFeeConfiguration::CHARGE, $result->getValue());
        }
    }
}
