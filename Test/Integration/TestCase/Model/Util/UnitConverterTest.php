<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ShippingCore\Test\Integration\TestCase\Model\Util;

use Magento\Directory\Helper\Data;
use Magento\Directory\Model\Currency;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Measure\Length;
use Magento\Framework\Measure\Weight;
use Magento\Shipping\Helper\Carrier;
use Magento\TestFramework\ObjectManager;
use Netresearch\ShippingCore\Api\Util\UnitConverterInterface;
use PHPUnit\Framework\TestCase;

class UnitConverterTest extends TestCase
{
    /**
     * @var $objectManager ObjectManager
     */
    private $objectManager;

    /**
     * @var UnitConverterInterface
     */
    private $unitConverter;

    /**
     * prepare object manager, add mocks
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();

        $rateUsdToEur = 0.9377;
        $rateGbpToEur = 1.1723;
        $rateGbpToUsd = 1.2494;

        $currencyMock = $this->getMockBuilder(Currency::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currencyMock
            ->expects($this->any())
            ->method('getRate')
            ->willReturnOnConsecutiveCalls($rateUsdToEur, $rateGbpToEur, $rateGbpToUsd);

        $currencyFactoryMock = $this->getMockBuilder(CurrencyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currencyFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($currencyMock);

        $carrierHelper = $this->objectManager->get(Carrier::class);

        // Create a properly configured directory helper
        $directoryHelper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the convertMonetaryValue method directly
        $directoryHelper->expects($this->any())
            ->method('currencyConvert')
            ->willReturnCallback(function ($amount, $from, $to) use ($rateUsdToEur, $rateGbpToEur, $rateGbpToUsd) {
                if ($from === 'USD' && $to === 'EUR') {
                    return $amount * $rateUsdToEur;
                } elseif ($from === 'GBP' && $to === 'EUR') {
                    return $amount * $rateGbpToEur;
                } elseif ($from === 'GBP' && $to === 'USD') {
                    return $amount * $rateGbpToUsd;
                }
                return $amount;
            });

        $this->unitConverter = $this->objectManager->create(UnitConverterInterface::class, [
            'currencyConverter' => $directoryHelper,
            'unitConverter' => $carrierHelper,
        ]);
    }

    /**
     * @magentoConfigFixture default_store general/locale/code en_US
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function convertDimension()
    {
        $valueInKm = 1;
        $valueInM = 1000;
        $valueInInches = 39370.079;

        $conversionResult = $this->unitConverter->convertDimension(
            $valueInKm,
            Length::KILOMETER,
            Length::METER
        );
        $this->assertEquals($valueInM, $conversionResult);

        $conversionResult = $this->unitConverter->convertDimension(
            $valueInKm,
            Length::KILOMETER,
            Length::INCH
        );
        $this->assertEquals($valueInInches, $conversionResult);
    }

    /**
     * @magentoConfigFixture default_store general/locale/code en_US
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function convertMoney()
    {
        $valueInEur = 1;
        $valueInUsd = 1.066;
        $valueInGbp = 0.853;

        $conversionResult = $this->unitConverter->convertMonetaryValue($valueInUsd, 'USD', 'EUR');
        $this->assertEquals($valueInEur, $conversionResult);

        $conversionResult = $this->unitConverter->convertMonetaryValue($valueInGbp, 'GBP', 'EUR');
        $this->assertEquals($valueInEur, $conversionResult);

        $conversionResult = $this->unitConverter->convertMonetaryValue($valueInGbp, 'GBP', 'USD');
        $this->assertEquals($valueInUsd, $conversionResult);
    }

    /**
     * @magentoConfigFixture default_store general/locale/code en_US
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function convertWeight()
    {
        $valueInKg = '1';
        $valueInG = '1000';
        $valueInLbs = '2.205';

        $conversionResult = $this->unitConverter->convertWeight(
            $valueInKg,
            Weight::KILOGRAM,
            Weight::GRAM
        );
        $this->assertEquals($valueInG, $conversionResult);

        $conversionResult = $this->unitConverter->convertWeight(
            $valueInKg,
            Weight::KILOGRAM,
            Weight::LBS
        );
        $this->assertEquals($valueInLbs, $conversionResult);
    }

    /**
     * @magentoConfigFixture default_store general/locale/code de_DE
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function handleDeLocaleWithPointSeparator()
    {
        $lang = getenv('HTTP_ACCEPT_LANGUAGE');

        putenv('HTTP_ACCEPT_LANGUAGE=de-DE');

        $valueInLbs = '3.2';
        $valueInG = 1451.496;

        $conversionResult = $this->unitConverter->convertWeight(
            $valueInLbs,
            Weight::LBS,
            Weight::GRAM
        );
        $this->assertEquals($valueInG, $conversionResult);

        if ($lang) {
            putenv("HTTP_ACCEPT_LANGUAGE=$lang");
        } else {
            putenv('HTTP_ACCEPT_LANGUAGE');
        }
    }

    /**
     * @magentoConfigFixture default_store general/locale/code de_DE
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function handleDeLocaleWithCommaSeparator()
    {
        $lang = getenv('HTTP_ACCEPT_LANGUAGE');

        putenv('HTTP_ACCEPT_LANGUAGE=de-DE');

        $valueInLbs = 3.2;
        $valueInG = 1451.496;

        $conversionResult = $this->unitConverter->convertWeight(
            $valueInLbs,
            Weight::LBS,
            Weight::GRAM
        );
        $this->assertEquals($valueInG, $conversionResult);

        if ($lang) {
            putenv("HTTP_ACCEPT_LANGUAGE=$lang");
        } else {
            putenv('HTTP_ACCEPT_LANGUAGE');
        }
    }
}
