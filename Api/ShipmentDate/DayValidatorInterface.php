<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShipmentDate;

/**
 * @api
 */
interface DayValidatorInterface
{
    /**
     * Returns TRUE if the given date is valid for this validator or FALSE otherwise.
     *
     * @param \DateTimeInterface $dateTime The date/time object to check
     * @param mixed $store The store to use for validation
     *
     * @return bool
     */
    public function validate(\DateTimeInterface $dateTime, mixed $store = null): bool;
}
