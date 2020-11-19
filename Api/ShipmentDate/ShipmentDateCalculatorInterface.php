<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShipmentDate;

/**
 * @api
 */
interface ShipmentDateCalculatorInterface
{
    /**
     * Retrieve the next possible shipment date from the given list of dates.
     *
     * The current time will be considered, any other rules may be added via validators.
     *
     * @param \DateTime[] $dates
     * @param mixed $store
     *
     * @return \DateTime
     * @throws \RuntimeException
     */
    public function getDate(array $dates, $store = null): \DateTime;
}