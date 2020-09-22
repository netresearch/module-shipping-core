<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

use Magento\Directory\Helper\Data;
use Magento\Framework\Locale\FormatInterface;
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

    public function convertDimension(float $value, string $unitIn, string $unitOut): float
    {
        $localFormatValue = (float) $this->localeFormat->getNumber($value);
        $converted = (float) $this->unitConverter->convertMeasureDimension($localFormatValue, $unitIn, $unitOut);

        return round($converted, self::CONVERSION_PRECISION);
    }

    public function convertMonetaryValue(float $value, string $unitIn, string $unitOut): float
    {
        $amount = $this->currencyConverter->currencyConvert($value, $unitIn, $unitOut);

        return round($amount, self::CONVERSION_PRECISION);
    }

    public function convertWeight(float $value, string $unitIn, string $unitOut): float
    {
        $value = (float) $this->localeFormat->getNumber($value);
        if ($value === 0.0) {
            return $value;
        }

        $converted = (float) $this->unitConverter->convertMeasureWeight($value, $unitIn, $unitOut);

        return round($converted, self::CONVERSION_PRECISION);
    }

    public function normalizeWeightUnit($weightUnit): string
    {
        switch (strtoupper($weightUnit)) {
            case \Zend_Measure_Weight::KILOGRAM:
            case 'KGS':
                return 'kg';
            case \Zend_Measure_Weight::POUND:
            case 'LBS':
                return 'lb';
            default:
                return $weightUnit;
        }
    }

    public function normalizeDimensionUnit($dimensionUnit): string
    {
        switch (strtoupper($dimensionUnit)) {
            case \Zend_Measure_Length::CENTIMETER:
                return 'cm';
            case \Zend_Measure_Length::INCH:
                return 'in';
            default:
                return $dimensionUnit;
        }
    }
}
