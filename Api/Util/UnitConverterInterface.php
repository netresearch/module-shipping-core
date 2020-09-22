<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Util;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
interface UnitConverterInterface
{
    public const CONVERSION_PRECISION = 3;

    /**
     * Convert dimension from one unit of measurement into another.
     *
     * @param float $value
     * @param string $unitIn
     * @param string $unitOut
     *
     * @return float
     */
    public function convertDimension(float $value, string $unitIn, string $unitOut): float;

    /**
     * Convert monetary value from one currency into another.
     *
     * @param float $value
     * @param string $unitIn
     * @param string $unitOut
     *
     * @return float
     * @throws NoSuchEntityException Conversion rate not available
     */
    public function convertMonetaryValue(float $value, string $unitIn, string $unitOut): float;

    /**
     * Convert weight from one unit of measurement into another.
     *
     * @param float $value
     * @param string $unitIn
     * @param string $unitOut
     *
     * @return float
     */
    public function convertWeight(float $value, string $unitIn, string $unitOut): float;

    /**
     * Returns lowercase two letter representation of weight unit, e.g. KILOGRAM => kg
     *
     * @param $weightUnit
     * @return string
     */
    public function normalizeWeightUnit($weightUnit): string;

    /**
     * Returns lowercase two letter representation of given dimension unit
     *
     * @param $dimensionUnit
     * @return string
     */
    public function normalizeDimensionUnit($dimensionUnit): string;
}
