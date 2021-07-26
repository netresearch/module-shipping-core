<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Model\Util;

use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Model\Util\CustomsRegulationsProvider;
use PHPUnit\Framework\TestCase;

class CustomsRegulationsProviderTest extends TestCase
{
    /**
     * @var ShippingConfigInterface
     */
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $euCountries = explode(',', 'AT,BE,BG,HR,CY,CZ,DK,EE,FI,FR,DE,GR,HU,IE,IT,LV,LT,LU,MT,NL,PL,PT,RO,SK,SI,ES,SE');
        $this->config = $this->createConfiguredMock(ShippingConfigInterface::class, [
            'getEuCountries' => $euCountries,
        ]);
    }

    /**
     * Assert that the provider recognizes destinations that do not require special handling.
     *
     * @test
     */
    public function regularDestination()
    {
        $provider = new CustomsRegulationsProvider($this->config);
        $origin = 'DE';

        self::assertEmpty($provider->getCustomsRegulations($origin, 'ES', '28970'));
        self::assertEmpty($provider->getCustomsRegulations($origin, 'GB', 'W1U 6BF'));
        self::assertEmpty($provider->getCustomsRegulations($origin, 'FR', '59000'));
    }

    /**
     * Assert that the provider recognizes destination areas that do not need a customs form.
     *
     * @test
     */
    public function nonDutiableDestination()
    {
        $provider = new CustomsRegulationsProvider($this->config);
        $origin = 'DE';

        self::assertSame(
            CustomsRegulationsProvider::NON_DUTIABLE,
            $provider->getCustomsRegulations($origin, 'GB', 'BT6 0BZ')
        );
    }

    /**
     * Assert that the provider recognizes destination areas that require a customs form.
     *
     * @test
     */
    public function dutiableDestination()
    {
        $provider = new CustomsRegulationsProvider($this->config);
        $origin = 'DE';

        self::assertSame(
            CustomsRegulationsProvider::DUTIABLE,
            $provider->getCustomsRegulations($origin, 'DE', '27498')
        );
        self::assertSame(
            CustomsRegulationsProvider::DUTIABLE,
            $provider->getCustomsRegulations($origin, 'DE', '78266')
        );
        self::assertSame(
            CustomsRegulationsProvider::DUTIABLE,
            $provider->getCustomsRegulations($origin, 'IT', '22060')
        );
        self::assertSame(
            CustomsRegulationsProvider::DUTIABLE,
            $provider->getCustomsRegulations($origin, 'ES', '35005')
        );
        self::assertSame(
            CustomsRegulationsProvider::DUTIABLE,
            $provider->getCustomsRegulations($origin, 'ES', '38670')
        );
    }
}
