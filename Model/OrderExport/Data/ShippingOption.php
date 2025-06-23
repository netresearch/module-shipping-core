<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\OrderExport\Data;

use Netresearch\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface;
use Netresearch\ShippingCore\Api\Data\OrderExport\ServiceDataInterface;
use Netresearch\ShippingCore\Api\Data\OrderExport\ShippingOptionInterface;

class ShippingOption implements ShippingOptionInterface
{
    /**
     * @var KeyValueObjectInterface[]
     */
    private $package;

    /**
     * @var ServiceDataInterface[]
     */
    private $services;

    /**
     * PackageData constructor.
     *
     * @param KeyValueObjectInterface[] $package
     * @param ServiceDataInterface[] $services
     */
    public function __construct(array $package, array $services)
    {
        $this->package = $package;
        $this->services = $services;
    }

    /**
     * @return KeyValueObjectInterface[]
     */
    #[\Override]
    public function getPackage(): array
    {
        return $this->package;
    }

    /**
     * @param KeyValueObjectInterface[] $package
     * @return $this
     */
    public function setPackage(array $package): self
    {
        $this->package = $package;

        return $this;
    }

    /**
     * @return ServiceDataInterface[]
     */
    #[\Override]
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @param ServiceDataInterface[] $services
     * @return $this
     */
    public function setServices(array $services): self
    {
        $this->services = $services;

        return $this;
    }
}
