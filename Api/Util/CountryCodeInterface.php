<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Util;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Utility for retrieving a country's ISO 3166 ALPHA-3 code
 *
 * @api
 *
 * @deprecated
 * @see CountryCodeConverterInterface
 */
interface CountryCodeInterface
{
    /**
     * Obtain three-letter country code from two-letter country code.
     *
     * @param string $iso2Code
     * @return string
     * @throws NoSuchEntityException
     */
    public function getIso3Code(string $iso2Code): string;
}
