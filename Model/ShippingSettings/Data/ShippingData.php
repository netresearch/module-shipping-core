<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;

class ShippingData implements ShippingDataInterface
{
    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[]
     */
    private $carriers;

    /**
     * ShippingData constructor.
     *
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[] $carriers
     */
    public function __construct(array $carriers = [])
    {
        $this->carriers = $carriers;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[]
     */
    public function getCarriers(): array
    {
        return $this->carriers;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[] $carriers
     */
    public function setCarriers(array $carriers)
    {
        $this->carriers = $carriers;
    }
}
