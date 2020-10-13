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
     * ShipmentDate constructor.
     *
     * @param TimezoneInterface $timezone
     * @param DayValidatorInterface[] $dayValidators A list of validators used to check if the current
     *                                               date can be uses as the shipping date.
     */
    public function __construct(
        TimezoneInterface $timezone,
        array $dayValidators = []
    ) {
        $this->timezone = $timezone;
        $this->dayValidators = $dayValidators;
    }

    public function getDate(array $dates, $store = null): \DateTime
    {
        usort($dates, function (\DateTime $a, \DateTime $b) {
            return $a->getTimestamp() > $b->getTimestamp();
        });

        $currentTime =  $this->timezone->scopeDate($store, null, true);
        foreach ($dates as $shipmentTime) {
            if ($currentTime > $shipmentTime) {
                continue;
            }

            // Apply all validators to the current date/time
            foreach ($this->dayValidators as $dayValidator) {
                // All validators have to agree that a date is valid before it can be used
                if (!$dayValidator->validate($shipmentTime, $store)) {
                    continue;
                }
            }

            return $shipmentTime;
        }

        throw new \RuntimeException('No applicable shipment date found.');
    }
}
