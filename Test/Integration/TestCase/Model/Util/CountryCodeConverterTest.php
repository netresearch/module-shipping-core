<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ShippingCore\Test\Integration\TestCase\Model\Util;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\ObjectManager;
use Netresearch\ShippingCore\Api\Util\CountryCodeConverterInterface;
use Netresearch\ShippingCore\Model\Util\Alpha2Converter;
use Netresearch\ShippingCore\Model\Util\Alpha3Converter;
use PHPUnit\Framework\TestCase;

class CountryCodeConverterTest extends TestCase
{
    /**
     * @var $objectManager ObjectManager
     */
    private $objectManager;

    public static function countryCodesProvider(): array
    {
        return [
            ['DE', 'DEU'],
            ['AD', 'AND'],
            ['AU', 'AUS'],
            ['DK', 'DNK'],
            ['HR', 'HRV'],
            ['GB', 'GBR'],
            ['ES', 'ESP'],
            ['IT', 'ITA'],
        ];
    }

    public static function invalidCodesProvider(): array
    {
        return [
            ['', ''],
            ['RB', ''],
            ['XS', ''],
            ['', 'LOL'],
            ['', 'FOO'],
        ];
    }

    /**
     * prepare object manager, add mocks
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * @throws NoSuchEntityException
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('countryCodesProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function toAlpha2(string $alpha2, string $alpha3)
    {
        $this->objectManager->configure(['preferences' => [
            CountryCodeConverterInterface::class => Alpha2Converter::class,
        ]]);

        /** @var CountryCodeConverterInterface $converter */
        $converter = $this->objectManager->create(CountryCodeConverterInterface::class);

        self::assertSame($alpha2, $converter->convert($alpha2));
        self::assertSame($alpha2, $converter->convert($alpha3));
    }

    /**
     * @throws NoSuchEntityException
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('countryCodesProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function toAlpha3(string $alpha2, string $alpha3)
    {
        $this->objectManager->configure(['preferences' => [
            CountryCodeConverterInterface::class => Alpha3Converter::class,
        ]]);

        /** @var CountryCodeConverterInterface $converter */
        $converter = $this->objectManager->create(CountryCodeConverterInterface::class);

        self::assertSame($alpha3, $converter->convert($alpha2));
        self::assertSame($alpha3, $converter->convert($alpha3));
    }

    /**
     * @throws NoSuchEntityException
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidCodesProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function invalidToAlpha3(string $alpha2, string $alpha3)
    {
        $this->objectManager->configure(['preferences' => [
            CountryCodeConverterInterface::class => Alpha2Converter::class,
        ]]);

        /** @var CountryCodeConverterInterface $converter */
        $converter = $this->objectManager->create(CountryCodeConverterInterface::class);

        if (empty($alpha2)) {
            // empty argument is supposed to return empty (no error)
            self::assertSame('', $converter->convert($alpha2));
        } elseif (empty($alpha3)) {
            // unavailable alpha3 is supposed to throw exception
            self::expectException(NoSuchEntityException::class);
            $converter->convert($alpha2);
        }
    }

    /**
     * @throws NoSuchEntityException
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidCodesProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function invalidAlpha3(string $alpha2, string $alpha3)
    {
        $this->objectManager->configure(['preferences' => [
            CountryCodeConverterInterface::class => Alpha3Converter::class,
        ]]);

        /** @var CountryCodeConverterInterface $converter */
        $converter = $this->objectManager->create(CountryCodeConverterInterface::class);

        if (empty($alpha3)) {
            // empty argument is supposed to return empty (no error)
            self::assertSame('', $converter->convert($alpha3));
        } elseif (empty($alpha2)) {
            // unavailable alpha3 is supposed to throw exception
            self::expectException(NoSuchEntityException::class);
            $converter->convert($alpha3);
        }
    }
}
