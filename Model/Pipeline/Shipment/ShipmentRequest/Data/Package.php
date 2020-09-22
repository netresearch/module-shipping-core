<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\Data;

use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageAdditionalInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageInterface;

class Package implements PackageInterface
{
    /**
     * Product used for the package, e.g. V01PAK, PLT, etc.
     *
     * @var string
     */
    private $productCode;

    /**
     * Packaging type, e.g. "C5 Letter", "Small Box", etc.
     *
     * @var string
     */
    private $containerType;

    /**
     * @var string
     */
    private $weightUom;

    /**
     * @var string
     */
    private $dimensionsUom;

    /**
     * @var float
     */
    private $weight;

    /**
     * @var float|null
     */
    private $length;

    /**
     * @var float|null
     */
    private $width;

    /**
     * @var float|null
     */
    private $height;

    /**
     * @var float|null
     */
    private $customsValue;

    /**
     * @var string
     */
    private $exportDescription;

    /**
     * @var string
     */
    private $termsOfTrade;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $contentExplanation;

    /**
     * @var PackageAdditionalInterface
     */
    private $packageAdditional;

    public function __construct(
        string $productCode,
        string $containerType,
        string $weightUom,
        string $dimensionsUom,
        float $weight,
        float $length = null,
        float $width = null,
        float $height = null,
        float $customsValue = null,
        string $exportDescription = '',
        string $termsOfTrade = '',
        string $contentType = '',
        string $contentExplanation = '',
        PackageAdditionalInterface $packageAdditional = null
    ) {
        $this->productCode = $productCode;
        $this->containerType = $containerType;
        $this->weightUom = $weightUom;
        $this->dimensionsUom = $dimensionsUom;
        $this->weight = $weight;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->customsValue = $customsValue;
        $this->exportDescription = $exportDescription;
        $this->termsOfTrade = $termsOfTrade;
        $this->contentType = $contentType;
        $this->contentExplanation = $contentExplanation;
        $this->packageAdditional = $packageAdditional ?? new PackageAdditional();
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getContainerType(): string
    {
        return $this->containerType;
    }

    /**
     * Obtain weight unit of measurement.
     *
     * Note: Shipment request passes them in as \Zend_Measure values.
     *
     * @return string
     */
    public function getWeightUom(): string
    {
        return $this->weightUom;
    }

    public function getDimensionsUom(): string
    {
        return $this->dimensionsUom;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function getCustomsValue(): ?float
    {
        return $this->customsValue;
    }

    public function getExportDescription(): string
    {
        return $this->exportDescription;
    }

    public function getTermsOfTrade(): string
    {
        return $this->termsOfTrade;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContentExplanation(): string
    {
        return $this->contentExplanation;
    }

    public function getPackageAdditional(): PackageAdditionalInterface
    {
        return $this->packageAdditional;
    }
}
