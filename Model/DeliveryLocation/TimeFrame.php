<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\DeliveryLocation;

use Netresearch\ShippingCore\Api\Data\DeliveryLocation\TimeFrameInterface;

class TimeFrame implements TimeFrameInterface
{
    /**
     * @var string
     */
    private $opens;

    /**
     * @var string
     */
    private $closes;

    /**
     * @return string
     */
    #[\Override]
    public function getOpens(): string
    {
        return $this->opens;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getCloses(): string
    {
        return $this->closes;
    }

    /**
     * @param string $opens
     */
    public function setOpens(string $opens): void
    {
        $this->opens = $opens;
    }

    /**
     * @param string $closes
     */
    public function setCloses(string $closes): void
    {
        $this->closes = $closes;
    }
}
