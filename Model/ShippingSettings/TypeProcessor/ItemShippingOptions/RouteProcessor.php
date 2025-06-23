<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ItemShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ItemShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\RouteMatcher;

class RouteProcessor implements ItemShippingOptionsProcessorInterface
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
     * @param ItemShippingOptionsInterface[] $itemOptions
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return ItemShippingOptionsInterface[]
     */
    #[\Override]
    public function process(
        string $carrierCode,
        array $itemOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ?ShipmentInterface $shipment = null
    ): array {
        $shippingOrigin = $this->config->getOriginCountry($storeId);

        foreach ($itemOptions as $itemOption) {
            $shippingOptions = array_filter(
                $itemOption->getShippingOptions(),
                function (ShippingOptionInterface $shippingOption) use ($shippingOrigin, $countryCode, $storeId) {
                    return $this->routeMatcher->match(
                        $shippingOption->getRoutes(),
                        $shippingOrigin,
                        $countryCode,
                        $storeId
                    );
                }
            );
            $itemOption->setShippingOptions($shippingOptions);
        }

        return $itemOptions;
    }
}
