<?php

namespace Netresearch\ShippingCore\Api\ShipmentDate;

/**
 * @api
 */
interface CutOffTimeConverterInterface
{
    /**
     * Convert array of cut-off-times into datetime objects.
     *
     * @param \DateTimeInterface $currentDate
     * @param string[] $cutOffTimes Times (H:i), indexed by ISO-8601 day of week, e.g. ['1' => '16:00', '3' => '18:00']
     * @return \DateTimeInterface[] Cut-off dates, indexed by ISO-8601 day of week
     */
    public function convert(\DateTimeInterface $currentDate, array $cutOffTimes): array;
}
