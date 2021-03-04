<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Integration\Model;

use Netresearch\ShippingCore\Model\Config\ShippingConfig;
use Netresearch\ShippingCore\Model\ShippingSettings\RouteMatcher;
use Netresearch\ShippingCore\Test\Provider\RouteProvider;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class RouteMatcherTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManger;

    /**
     * @var ShippingConfig
     */
    private $config;

    /**
     * @var RouteMatcher
     */
    private $routeMatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManger = Bootstrap::getObjectManager();
        $this->config = $this->objectManger->create(ShippingConfig::class);
        $this->routeMatcher = $this->objectManger->create(RouteMatcher::class, [$this->config]);
    }

    public function getRouteData(): array
    {
        return RouteProvider::getRoutes();
    }

    /**
     * @dataProvider getRouteData
     *
     * @param array $routes
     * @param string $shippingOrigin
     * @param string $destination
     * @param int $storeId
     * @param bool $expected
     */
    public function testMatch(
        $routes,
        $shippingOrigin,
        $destination,
        $storeId,
        $expected
    ) {
        $result = $this->routeMatcher->match($routes, $shippingOrigin, $destination, $storeId);
        $this->assertSame($expected, $result);
    }
}
