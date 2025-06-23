<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

use Magento\Directory\Helper\Data;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Measure\Length;
use Magento\Framework\Measure\Weight;
use Magento\Shipping\Helper\Carrier;
use Netresearch\ShippingCore\Api\Util\UnitConverterInterface;

class UnitConverter implements UnitConverterInterface
{
    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var Data
     */
    private $currencyConverter;

    /**
     * @var Carrier
     */
    private $unitConverter;

    public function __construct(
        FormatInterface $localeFormat,
        Data $currencyConverter,
        Carrier $unitConverter
    ) {
        $this->localeFormat = $localeFormat;
        $this->currencyConverter = $currencyConverter;
        $this->unitConverter = $unitConverter;
    }

    #[\Override]
    public function convertDimension(float $value, string $unitIn, string $unitOut): float
    {
        $localFormatValue = (float) $this->localeFormat->getNumber($value);
        $converted = (float) $this->unitConverter->convertMeasureDimension($localFormatValue, $unitIn, $unitOut);

        return round($converted, self::CONVERSION_PRECISION);
    }

    #[\Override]
    public function convertMonetaryValue(float $value, string $unitIn, string $unitOut): float
    {
        $amount = $this->currencyConverter->currencyConvert($value, $unitIn, $unitOut);

        return round($amount, self::CONVERSION_PRECISION);
    }

    #[\Override]
    public function convertWeight(float $value, string $unitIn, string $unitOut): float
    {
        $value = (float) $this->localeFormat->getNumber($value);
        if ($value === 0.0) {
            return $value;
        }

        $converted = (float) $this->unitConverter->convertMeasureWeight($value, $unitIn, $unitOut);

        return round($converted, self::CONVERSION_PRECISION);
    }

    #[\Override]
    public function normalizeWeightUnit($weightUnit): string
    {
        return match (strtoupper((string) $weightUnit)) {
            Weight::KILOGRAM, 'KGS' => 'kg',
            Weight::POUND, 'LBS' => 'lb',
            default => $weightUnit,
        };
    }

    #[\Override]
    public function normalizeDimensionUnit($dimensionUnit): string
    {
        return match (strtoupper((string) $dimensionUnit)) {
            Length::CENTIMETER => 'cm',
            Length::INCH => 'in',
            default => $dimensionUnit,
        };
    }
}
