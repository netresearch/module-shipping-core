<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ShippingSettings;

/**
 * Interface ShippingOptionInterface
 *
 * A DTO with the rendering information for an individual shipping option with potentially multiple inputs.
 *
 * @api
 */
interface ShippingOptionInterface
{
    /**
     * Obtain shipping option code.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Obtain shipping option available config path.
     *
     * @return string
     */
    public function getAvailable(): string;

    /**
     * Obtain shipping option name.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Obtain a list of inputs for displaying the shipping option and its values.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[]
     */
    public function getInputs(): array;

    /**
     * Obtain routes the shipping option can be booked with.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[]
     */
    public function getRoutes(): array;

    /**
     * Retrieve the sort order of the shipping option relative to other shipping options of the current carrier.
     *
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * Returns an array of item ids (order/quote/shipment) that result in the current shipping option being available.
     *
     * @return int[]
     */
    public function getRequiredItemIds(): array;

    /**
     * @param string $code
     *
     * @return void
     */
    public function setCode(string $code);

    /**
     * @param string $available
     *
     * @return void
     */
    public function setAvailable(string $available);

    /**
     * @param string $label
     *
     * @return void
     */
    public function setLabel(string $label);

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[] $inputs
     *
     * @return void
     */
    public function setInputs(array $inputs);

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[] $routes
     *
     * @return void
     */
    public function setRoutes(array $routes);

    /**
     * @param int $sortOrder
     *
     * @return void
     */
    public function setSortOrder(int $sortOrder);

    /**
     * @param int[] $requiredItemIds
     *
     * @return void
     */
    public function setRequiredItemIds(array $requiredItemIds);
}
