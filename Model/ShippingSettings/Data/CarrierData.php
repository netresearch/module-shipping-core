<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;

class CarrierData implements CarrierDataInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\MetadataInterface|null
     */
    private $metadata;

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    private $packageOptions = [];

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface[]
     */
    private $itemOptions = [];

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    private $serviceOptions = [];

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface[]
     */
    private $compatibilityData = [];

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\MetadataInterface|null
     */
    public function getMetadata(): ?MetadataInterface
    {
        return $this->metadata;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    public function getPackageOptions(): array
    {
        return $this->packageOptions;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface[]
     */
    public function getItemOptions(): array
    {
        return $this->itemOptions;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    public function getServiceOptions(): array
    {
        return $this->serviceOptions;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface[]
     */
    public function getCompatibilityData(): array
    {
        return $this->compatibilityData;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\MetadataInterface $metadata
     */
    public function setMetadata(MetadataInterface $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[] $packageOptions
     */
    public function setPackageOptions(array $packageOptions): void
    {
        $this->packageOptions = $packageOptions;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface[] $itemOptions
     */
    public function setItemOptions(array $itemOptions): void
    {
        $this->itemOptions = $itemOptions;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[] $serviceOptions
     */
    public function setServiceOptions(array $serviceOptions): void
    {
        $this->serviceOptions = $serviceOptions;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface[] $compatibilityData
     */
    public function setCompatibilityData(array $compatibilityData): void
    {
        $this->compatibilityData = $compatibilityData;
    }
}
