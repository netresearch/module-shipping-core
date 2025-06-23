<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShipmentDate;

use Netresearch\ShippingCore\Api\ShipmentDate\CutOffTimeConverterInterface;

class CutOffTimeConverter implements CutOffTimeConverterInterface
{
    #[\Override]
    public function convert(\DateTimeInterface $currentDate, array $cutOffTimes): array
    {
        if ($currentDate instanceof \DateTime) {
            $currentDate = \DateTimeImmutable::createFromMutable($currentDate);
        }

        $cutOffDates = [];
        for ($i = 0; $i <= 6; $i++) {
            $cutOffDate = $currentDate->modify("+$i day");
            $weekDay = $cutOffDate->format('N');
            if (!isset($cutOffTimes[$weekDay])) {
                // no cut-off configured for the given day, next.
                continue;
            }

            if (str_contains($cutOffTimes[$weekDay], ':')) {
                // configured format is hh:mm
                $cutOffTime =  explode(':', $cutOffTimes[$weekDay]);
            } else {
                // configured format is hh
                $cutOffTime = [$cutOffTimes[$weekDay], '00'];
            }

            list($hours, $minutes) = array_map('intval', $cutOffTime);
            $cutOffDate = $cutOffDate->setTime($hours, $minutes);
            if ($cutOffDate < new \DateTime('now')) {
                $cutOffDate = $cutOffDate->modify("+1 week");
            }
            $cutOffDates[$weekDay] = $cutOffDate;
        }

        return $cutOffDates;
    }
}
