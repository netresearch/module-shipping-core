<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest;

/**
 * @api
 */
interface PackageInterface
{
    /**
     * Obtain product to be used for the package.
     *
     * @return string
     */
    public function getProductCode(): string;

    /**
     * Obtain pre-defined packaging name.
     *
     * @return string
     */
    public function getContainerType(): string;

    /**
     * Obtain weight unit of measurement.
     *
     * Note: Shipment request passes them in as \Zend_Measure values.
     *
     * @return string
     */
    public function getWeightUom(): string;

    /**
     * Obtain dimensions unit of measurement.
     *
     * Note: Shipment request passes them in as \Zend_Measure values.
     *
     * @return string
     */
    public function getDimensionsUom(): string;

    /**
     * Obtain package weight.
     *
     * @return float
     */
    public function getWeight(): float;

    /**
     * Obtain package length (optional).
     *
     * @return float|null
     */
    public function getLength(): ?float;

    /**
     * Obtain package width (optional).
     *
     * @return float|null
     */
    public function getWidth(): ?float;

    /**
     * Obtain package height (optional).
     *
     * @return float|null
     */
    public function getHeight(): ?float;

    /**
     * Obtain package customs value (optional).
     *
     * @return float|null
     */
    public function getCustomsValue(): ?float;

    /**
     * Obtain package customs declaration content type (optional).
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Obtain package customs declaration content description (optional).
     *
     * @return string
     */
    public function getContentExplanation(): string;

    /**
     * @return PackageAdditionalInterface
     */
    public function getPackageAdditional(): PackageAdditionalInterface;
}
