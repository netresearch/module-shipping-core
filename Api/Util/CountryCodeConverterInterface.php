<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Util;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Utility for retrieving a country's ISO 3166 code
 *
 * @api
 */
interface CountryCodeConverterInterface
{
    /**
     * Obtain normalized country code.
     *
     * @param string $countryCode
     * @return string
     * @throws NoSuchEntityException
     */
    public function convert(string $countryCode): string;
}
