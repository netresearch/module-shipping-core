<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption;

/**
 * Interface RouteInterface
 *
 * @api
 */
interface RouteInterface
{
    /**
     * Get the allowed origin for a route. Will return an empty string if the route has no origin restriction.
     *
     * @return string
     */
    public function getOrigin(): string;

    /**
     * Get a list of country codes of allowed destination countries. The special "intl" code is interpreted as all
     * countries, the code "eu" is expanded to a list of countries in the EU.
     *
     * @return string[]
     */
    public function getIncludeDestinations(): array;

    /**
     * Get a list of country codes of prohibited destination countries. The special "intl" code is interpreted as all
     * countries, the code "eu" is expanded to a list of countries in the EU.
     *
     * @return string[]
     */
    public function getExcludeDestinations(): array;

    /**
     * @param string $origin
     *
     * @return void
     */
    public function setOrigin(string $origin): void;

    /**
     * @param string[] $includeDestinations
     *
     * @return void
     */
    public function setIncludeDestinations(array $includeDestinations): void;

    /**
     * @param string[] $excludeDestinations
     *
     * @return void
     */
    public function setExcludeDestinations(array $excludeDestinations): void;
}
