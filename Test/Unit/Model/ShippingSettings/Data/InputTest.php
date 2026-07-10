<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Model\ShippingSettings\Data\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    public function testInputIsNeitherDisabledNorLockedByDefault(): void
    {
        $input = new Input();

        self::assertFalse($input->isDisabled());
        self::assertFalse($input->isLocked());
    }

    public function testLockedCanBeToggledIndependentlyOfDisabled(): void
    {
        $input = new Input();

        $input->setDisabled(true);
        self::assertTrue($input->isDisabled());
        self::assertFalse($input->isLocked(), 'disabling an input must not imply a lock');

        $input->setLocked(true);
        self::assertTrue($input->isLocked());

        $input->setLocked(false);
        self::assertFalse($input->isLocked());
        self::assertTrue($input->isDisabled(), 'unlocking an input must not enable it');
    }
}
