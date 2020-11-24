<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShipmentDate;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Netresearch\ShippingCore\Api\ShipmentDate\DayValidatorInterface;
use Netresearch\ShippingCore\Api\ShipmentDate\ShipmentDateCalculatorInterface;

class ShipmentDateCalculator implements ShipmentDateCalculatorInterface
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var DayValidatorInterface[]
     */
    private $dayValidators;

    /**
     * ShipmentDateCalculator constructor.
     *
     * @param TimezoneInterface $timezone
     * @param DayValidatorInterface[] $dayValidators Validators used to check if a date can be used as shipping date.
     */
    public function __construct(TimezoneInterface $timezone, array $dayValidators = [])
    {
        $this->timezone = $timezone;
        $this->dayValidators = $dayValidators;
    }

    public function getDate(array $dropOffTimes, $store = null): \DateTimeInterface
    {
        usort($dropOffTimes, function (\DateTimeInterface $a, \DateTimeInterface $b) {
            return $a->getTimestamp() > $b->getTimestamp();
        });

        $currentTime = $this->timezone->scopeDate($store, null, true);

        // check three weeks ahead for a match
        for ($week = 0; $week < 3; $week++) {
            /** @var \DateTime|\DateTimeImmutable $shipmentTime */
            foreach ($dropOffTimes as $shipmentTime) {
                if ($currentTime > $shipmentTime) {
                    continue;
                }

                if ($week > 0) {
                    $shipmentTime = $shipmentTime->modify(("+$week weeks"));
                }

                $isValid = true;
                // Apply all validators to the current date/time
                foreach ($this->dayValidators as $dayValidator) {
                    // All validators have to agree that a date is valid before it can be used
                    $isValid = $isValid && $dayValidator->validate($shipmentTime, $store);
                }

                if ($isValid) {
                    return $shipmentTime;
                }
            }
        }

        throw new \RuntimeException('No applicable shipment date found.');
    }
}
