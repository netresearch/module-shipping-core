<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Model\ShippingSettings\Config;

use Netresearch\ShippingCore\Model\ShippingSettings\Config\Converter;
use Netresearch\ShippingCore\Model\Util\ConstantResolver;
use Netresearch\ShippingCore\Test\Unit\Provider\ShippingSettingsProvider;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    const CODE_PARCEL_ANNOUNCEMENT = 'parcelAnnouncement';

    /**
     * @var string[]
     */
    private $nesting = [''];

    public function dataProvider(): array
    {
        return [
            'test case 1' => ShippingSettingsProvider::getShippingSettings(),
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider
     *
     * @param string $xml shipping settings xml
     * @param mixed[] $expected expected shipping settings array representation
     */
    public function testConvert(string $xml, array $expected)
    {
        $xmlDoc = new \DOMDocument();
        $xmlDoc->loadXML($xml);

        $subject = new Converter(new ConstantResolver());
        $result = $subject->convert($xmlDoc);

        $this->compareRecursive($expected, $result);
        $this->compareRecursive($result, $expected);
        self::assertSame($expected, $result);
    }

    /**
     * @param mixed|mixed[] $a
     * @param mixed|mixed[] $b
     */
    private function compareRecursive($a, $b)
    {
        foreach ($a as $aKey => $aValue) {
            self::assertArrayHasKey($aKey, $b, 'Keys don\'t match at ' . implode('/', $this->nesting));
            $bValue = $b[$aKey];
            if (is_array($bValue) && is_array($aValue)) {
                $this->nesting[] = $aKey;
                $this->compareRecursive($aValue, $bValue);
                array_pop($this->nesting);
            }
            self::assertSame($bValue, $aValue, 'Values don\'t match at ' . implode('/', $this->nesting));
        }
    }
}
