<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\DeliveryLocation;

/**
 * @api
 */
interface OpeningHoursInterface
{
    /**
     * @return \Netresearch\ShippingCore\Api\Data\DeliveryLocation\TimeFrameInterface[]
     */
    public function getTimeFrames(): array;

    /**
     * @return string
     */
    public function getDayOfWeek(): string;
}
