<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Provider;

class ShippingSettingsProvider
{
    /**
     * @return mixed[]
     */
    public static function getShippingSettings(): array
    {
        return [
            'xml' => file_get_contents(__DIR__ . '/_files/shipping_settings.xml'),
            'array' => \json_decode(file_get_contents(__DIR__ . '/_files/shipping_settings_expected.json'), true)
        ];
    }
}
