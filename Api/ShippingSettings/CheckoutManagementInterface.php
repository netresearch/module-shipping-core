<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;

/**
 * A service for setting and retrieving data during the checkout process.
 *
 * Use this service to retrieve any shipping options and other checkout data,
 * as well as to persist customer selections for shipping options.
 *
 * @api
 */
interface CheckoutManagementInterface
{
    /**
     * Retrieve the currently configured checkout data concerning the display of additional shipping options
     *
     * @param string $countryId
     * @param string $postalCode
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface
     */
    public function getCheckoutData(string $countryId, string $postalCode): ShippingDataInterface;

    /**
     * Persist a set of customer shipping option selections.
     *
     * @param int $cartId
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface[] $shippingOptionSelections
     * @return void
     */
    public function updateShippingOptionSelections(int $cartId, array $shippingOptionSelections): void;
}
