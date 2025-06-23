<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Netresearch\ShippingCore\Model\Config\ShippingConfig;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\Route;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\ShippingOption;
use Netresearch\ShippingCore\Model\ShippingSettings\RouteMatcher;
use Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions\RouteProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RouteProcessorTest extends TestCase
{
    /**
     * @var ShippingConfig|MockObject
     */
    private $configMock;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->configMock = $this->getMockBuilder(ShippingConfig::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->configMock->method('getEuCountries')->willReturn(['DE', 'AT', 'IT', 'UK']);
    }

    public static function dataProvider(): array
    {
        $optionNoRoute = new ShippingOption();
        $optionNoRoute->setCode('test');

        $routeEu = new Route();
        $routeEu->setIncludeDestinations(['eu']);
        $optionDestinationEu = new ShippingOption();
        $optionDestinationEu->setCode('test');
        $optionDestinationEu->setRoutes([$routeEu]);

        $routeNonEu = new Route();
        $routeNonEu->setOrigin('eu');
        $routeNonEu->setExcludeDestinations(['eu']);
        $optionDestinationNonEu = new ShippingOption();
        $optionDestinationNonEu->setCode('test');
        $optionDestinationNonEu->setRoutes([$routeNonEu]);

        $routeNonIntl = new Route();
        $routeNonIntl->setIncludeDestinations(['domestic']);
        $optionDestinationNonIntl = new ShippingOption();
        $optionDestinationNonIntl->setCode('test');
        $optionDestinationNonIntl->setRoutes([$routeNonIntl]);

        $routeDeToIntl = new Route();
        $routeDeToIntl->setOrigin('de');
        $routeDeToIntl->setExcludeDestinations(['domestic']);
        $optionDeToIntl = new ShippingOption();
        $optionDeToIntl->setCode('test');

        return [
            'de => us, no routes specified' => [
                'optionsData' => [$optionNoRoute],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'US',
                'expectedCount' => 1,
            ],
            'de => us, eu destination required' => [
                'optionsData' => [$optionDestinationEu],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'US',
                'expectedCount' => 0,
            ],
            'de => at, eu destination required' => [
                'optionsData' => [$optionDestinationEu],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'AT',
                'expectedCount' => 1,
            ],
            'de => us, eu destination not allowed' => [
                'optionsData' => [$optionDestinationNonEu],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'US',
                'expectedCount' => 1,
            ],
            'de => at, eu destination not allowed' => [
                'optionsData' => [$optionDestinationNonEu],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'AT',
                'expectedCount' => 0,
            ],
            'de => de, only domestic allowed' => [
                'optionsData' => [$optionDestinationNonIntl],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'DE',
                'expectedCount' => 1,
            ],
            'de => at, only domestic allowed' => [
                'optionsData' => [$optionDestinationNonIntl],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'AT',
                'expectedCount' => 0,
            ],
        ];
    }

    /**
     *
     * @param mixed[] $optionsData
     * @param string $originCountryId
     * @param string $destinationCountryId
     * @param int $expectedCount
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('dataProvider')]
    public function testProcess(
        array $optionsData,
        string $originCountryId,
        string $destinationCountryId,
        int $expectedCount
    ) {
        $this->configMock->method('getOriginCountry')->willReturn($originCountryId);
        $routeMatcher = new RouteMatcher($this->configMock);
        $subject = new RouteProcessor($this->configMock, $routeMatcher);
        $result = $subject->process(
            'foo_carrier',
            $optionsData,
            0,
            $destinationCountryId,
            '00000'
        );

        self::assertCount(
            $expectedCount,
            $result,
            'The route processor failed to filter the given shipping option correctly.'
        );
    }
}
