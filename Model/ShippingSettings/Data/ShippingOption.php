<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;

class ShippingOption implements ShippingOptionInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $available = '1';

    /**
     * @var string
     */
    private $label = '';

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[]
     */
    private $inputs = [];

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[]
     */
    private $routes = [];

    /**
     * @var int
     */
    private $sortOrder = 0;

    /**
     * @var int[]
     */
    private $requiredItemIds = [];

    /**
     * @return string
     */
    #[\Override]
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getAvailable(): string
    {
        return $this->available;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[]
     */
    #[\Override]
    public function getInputs(): array
    {
        return $this->inputs;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[]
     */
    #[\Override]
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return int
     */
    #[\Override]
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return int[]
     */
    #[\Override]
    public function getRequiredItemIds(): array
    {
        return $this->requiredItemIds;
    }

    /**
     * @param string $code
     */
    #[\Override]
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @param string $available
     */
    #[\Override]
    public function setAvailable(string $available): void
    {
        $this->available = $available;
    }

    /**
     * @param string $label
     */
    #[\Override]
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[] $inputs
     */
    #[\Override]
    public function setInputs(array $inputs): void
    {
        $this->inputs = $inputs;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[] $routes
     */
    #[\Override]
    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    /**
     * @param int $sortOrder
     */
    #[\Override]
    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @param int[] $requiredItemIds
     */
    #[\Override]
    public function setRequiredItemIds(array $requiredItemIds): void
    {
        $this->requiredItemIds = $requiredItemIds;
    }
}
