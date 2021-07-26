<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ItemShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterfaceFactory;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ItemShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Netresearch\ShippingCore\Model\Util\CustomsRegulationsProvider;

class ExtendCustomsOptionsRoutesProcessor implements ItemShippingOptionsProcessorInterface
{
    /**
     * @var ShippingConfigInterface
     */
    private $config;

    /**
     * @var CustomsRegulationsProvider
     */
    private $customsRegulationsProvider;

    /**
     * @var RouteInterfaceFactory
     */
    private $routeFactory;

    public function __construct(
        ShippingConfigInterface $config,
        CustomsRegulationsProvider $customsRegulationsProvider,
        RouteInterfaceFactory $routeFactory
    ) {
        $this->config = $config;
        $this->customsRegulationsProvider = $customsRegulationsProvider;
        $this->routeFactory = $routeFactory;
    }

    /**
     * Adjust the routes definition of customs-related shipping options.
     *
     * Currently it is not possible to define routes for areas (postal code ranges)
     * within a country. Some areas however require special customs regulations, e.g.
     * - Canary Islands need customs documents, mainland Spain does not.
     * - Northern Ireland needs no customs documents, rest of UK does.
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
    public function process(
        string $carrierCode,
        array $itemOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): array {
        if (!$shipment) {
            return $itemOptions;
        }

        $shippingOrigin = $this->config->getOriginCountry($storeId);
        $customsRegulations = $this->customsRegulationsProvider->getCustomsRegulations(
            $shippingOrigin,
            $countryCode,
            $postalCode
        );

        if (!$customsRegulations) {
            // no non-standard rules detected for current route, nothing to do.
            return $itemOptions;
        }

        foreach ($itemOptions as $itemOption) {
            $shippingOptions = $itemOption->getShippingOptions();
            $itemCustomsOption = $shippingOptions[Codes::ITEM_OPTION_CUSTOMS] ?? null;
            if (!$itemCustomsOption) {
                continue;
            }

            $routes = $itemCustomsOption->getRoutes();

            $route = $this->routeFactory->create();
            $route->setOrigin($shippingOrigin);
            ($customsRegulations === CustomsRegulationsProvider::NON_DUTIABLE)
                ? $route->setExcludeDestinations([$countryCode])
                : $route->setIncludeDestinations([$countryCode]);
            $routes[] = $route;

            $itemCustomsOption->setRoutes($routes);
        }

        return $itemOptions;
    }
}
