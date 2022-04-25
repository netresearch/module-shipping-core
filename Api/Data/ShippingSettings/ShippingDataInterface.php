<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ShippingSettings;

/**
 * Interface ShippingDataInterface
 *
 * A DTO with shipping options rendering data for carriers that support it
 *
 * @api
 */
interface ShippingDataInterface
{
    /**
     * Retrieve a list of carrier-specific data for rendering additional shipping options.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[]
     */
    public function getCarriers(): array;

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[] $carriers
     *
     * @return void
     */
    public function setCarriers(array $carriers): void;
}
