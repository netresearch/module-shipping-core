<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\RouteMatcher;

class RouteProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var ShippingConfigInterface
     */
    private $config;

    /**
     * @var RouteMatcher
     */
    private $routeMatcher;

    public function __construct(ShippingConfigInterface $config, RouteMatcher $routeValidator)
    {
        $this->config = $config;
        $this->routeMatcher = $routeValidator;
    }

    /**
     * Remove all shipping options that do not apply for the given route
     * (origin to destination), e.g. customs options for domestic routes.
     *
     * @param string $carrierCode
     * @param ShippingOptionInterface[] $shippingOptions
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return ShippingOptionInterface[]
     */
    #[\Override]
    public function process(
        string $carrierCode,
        array $shippingOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ?ShipmentInterface $shipment = null
    ): array {
        $shippingOrigin = $this->config->getOriginCountry($storeId);

        foreach ($shippingOptions as $index => $shippingOption) {
            $matchesRoute = $this->routeMatcher->match(
                $shippingOption->getRoutes(),
                $shippingOrigin,
                $countryCode,
                $storeId
            );

            if (!$matchesRoute) {
                unset($shippingOptions[$index]);
            }
        }

        return $shippingOptions;
    }
}
