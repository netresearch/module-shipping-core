<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface;

class Route implements RouteInterface
{
    /**
     * @var string
     */
    private $origin = '';

    /**
     * @var string[]
     */
    private $includeDestinations = [];

    /**
     * @var string[]
     */
    private $excludeDestinations = [];

    /**
     * @return string
     */
    #[\Override]
    public function getOrigin(): string
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     */
    #[\Override]
    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    /**
     * @return string[]
     */
    #[\Override]
    public function getIncludeDestinations(): array
    {
        return $this->includeDestinations;
    }

    /**
     * @param string[] $includeDestinations
     */
    #[\Override]
    public function setIncludeDestinations(array $includeDestinations): void
    {
        $this->includeDestinations = $includeDestinations;
    }

    /**
     * @return string[]
     */
    #[\Override]
    public function getExcludeDestinations(): array
    {
        return $this->excludeDestinations;
    }

    /**
     * @param string[] $excludeDestinations
     */
    #[\Override]
    public function setExcludeDestinations(array $excludeDestinations): void
    {
        $this->excludeDestinations = $excludeDestinations;
    }
}
