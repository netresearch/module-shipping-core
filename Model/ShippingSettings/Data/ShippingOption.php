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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getAvailable(): string
    {
        return $this->available;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[]
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return int[]
     */
    public function getRequiredItemIds(): array
    {
        return $this->requiredItemIds;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @param string $available
     */
    public function setAvailable(string $available): void
    {
        $this->available = $available;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[] $inputs
     */
    public function setInputs(array $inputs): void
    {
        $this->inputs = $inputs;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[] $routes
     */
    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    /**
     * @param int $sortOrder
     */
    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @param int[] $requiredItemIds
     */
    public function setRequiredItemIds(array $requiredItemIds): void
    {
        $this->requiredItemIds = $requiredItemIds;
    }
}
