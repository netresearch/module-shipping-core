<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Netresearch\ShippingCore\Model\Util\CustomsRegulationsProvider;

class ExtendCustomsOptionsRoutesProcessor implements ShippingOptionsProcessorInterface
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
     * @param ShippingOptionInterface[] $shippingOptions
     * @param int $storeId
     * @param string $countryCode Recipient country
     * @param string $postalCode Recipient postal code
     * @param ShipmentInterface|null $shipment Shipment entity if available
     *
     * @return ShippingOptionInterface[] Processed shipping option list
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
        if (!$shipment) {
            return $shippingOptions;
        }

        $shippingOrigin = $this->config->getOriginCountry($storeId);
        $customsRegulations = $this->customsRegulationsProvider->getCustomsRegulations(
            $shippingOrigin,
            $countryCode,
            $postalCode
        );

        if (!$customsRegulations) {
            // no non-standard rules detected for current route, nothing to do.
            return $shippingOptions;
        }

        foreach ($shippingOptions as $shippingOption) {
            if ($shippingOption->getCode() !== Codes::PACKAGE_OPTION_CUSTOMS) {
                continue;
            }

            $routes = $shippingOption->getRoutes();

            $route = $this->routeFactory->create();
            $route->setOrigin($shippingOrigin);
            ($customsRegulations === CustomsRegulationsProvider::NON_DUTIABLE)
                ? $route->setExcludeDestinations([$countryCode])
                : $route->setIncludeDestinations([$countryCode]);
            $routes[] = $route;

            $shippingOption->setRoutes($routes);
        }

        return $shippingOptions;
    }
}
