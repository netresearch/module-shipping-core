<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Model\Util;

use Netresearch\ShippingCore\Model\Util\StreetSplitter;
use Netresearch\ShippingCore\Test\Provider\StreetDataProvider;
use PHPUnit\Framework\TestCase;

class StreetSplitterTest extends TestCase
{
    /**
     * @return string[][][]
     */
    public static function getStreetData(): array
    {
        return StreetDataProvider::getStreetData();
    }

    /**
     *
     * @param string[] $street
     * @param string[] $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getStreetData')]
    public function testSplitStreet(array $street, array $expected)
    {
        $splitter = new StreetSplitter();
        $street = implode(', ', $street);
        $split = $splitter->splitStreet($street);
        $this->assertEquals($expected, $split);
    }
}
