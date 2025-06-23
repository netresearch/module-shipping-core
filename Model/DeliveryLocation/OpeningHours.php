<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\DeliveryLocation;

use Netresearch\ShippingCore\Api\Data\DeliveryLocation\TimeFrameInterface;
use Netresearch\ShippingCore\Api\Data\DeliveryLocation\OpeningHoursInterface;

class OpeningHours implements OpeningHoursInterface
{
    /**
     * @var string
     */
    private $dayOfWeek;

    /**
     * @var TimeFrameInterface[]
     */
    private $timeFrames;

    /**
     * @param string $dayOfWeek
     */
    public function setDayOfWeek(string $dayOfWeek): void
    {
        $this->dayOfWeek = $dayOfWeek;
    }

    /**
     * @param TimeFrameInterface[] $timeFrames
     */
    public function setTimeFrames(array $timeFrames): void
    {
        $this->timeFrames = $timeFrames;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    /**
     * @return TimeFrameInterface[]
     */
    #[\Override]
    public function getTimeFrames(): array
    {
        return  $this->timeFrames;
    }
}
