<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Model\Util;

use Netresearch\ShippingCore\Model\Util\ConstantResolver;
use PHPUnit\Framework\TestCase;

class ConstantResolverTest extends TestCase
{
    const TEST_CONST = 'test123';

    private $positiveTestLines = [
        '\Netresearch\ShippingCore\Test\Unit\Model\Util\ConstantResolverTest::TEST_CONST',
        'Netresearch\ShippingCore\Test\Unit\Model\Util\ConstantResolverTest::TEST_CONST',
    ];
    private $negativeTestLines = [
        '----Netresearch\ShippingCore\Test\Unit\Model\Util\ConstantResolverTest::TEST_CONST',
        'Netresearch\ShippingCore\Test\Unit\Model\Util\ConstantResolverTest::TEST-CONST',
        'Netresearch\ShippingCore\Test\Unit\Model\Util\ConstantResolverTest::TEST_CONST-',
        'CODE_PARCEL_ANNOUNCEMENT',
        'My:test',
        'this\is\a\test',
        'Parcel Announcement',
        'checkbox',
    ];

    private $brokenTestLines = [
        'Netresearch\ShippingCore\Test\Unit\Model\Util\ConverterTest::NONEXISTANT_CONSTANT',
        'Netresearch\Nonexistant\Class::CODE-PARCEL-ANNOUNCEMENT',
    ];

    private $combinedTestLines = [
        'Netresearch\ShippingCore\Test\Unit\Model\Util\ConstantResolverTest::TEST_CONST.enabled',
        'Netresearch\ShippingCore\Test\Unit\Model\Util\ConstantResolverTest::TEST_CONST.2cool4school',
        'Netresearch\ShippingCore\Test\Unit\Model\Util\ConstantResolverTest::TEST_CONST.Netresearch\ShippingCore\Test\Unit\Model\Util\ConstantResolverTest::TEST_CONST',
    ];

    public function testResolve()
    {
        $subject = new ConstantResolver();

        foreach ($this->positiveTestLines as $line) {
            self::assertSame(
                $subject->resolve($line),
                self::TEST_CONST,
                "preg_match does not detect '$line' as a constant."
            );
        }
        foreach ($this->negativeTestLines as $line) {
            self::assertSame(
                $subject->resolve($line),
                $line,
                "Constant Resolver wrongly detects '$line' as a constant."
            );
        }

        foreach ($this->combinedTestLines as $line) {
            $result = $subject->resolve($line);
            self::assertNotFalse(strpos($result, self::TEST_CONST));
            self::assertNotSame($line, $result);
        }

        $this->expectException(\RuntimeException::class);
        foreach ($this->brokenTestLines as $line) {
            $subject->resolve($line);
        }
    }
}
