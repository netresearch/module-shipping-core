<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config\Backend;

use Magento\Framework\App\Config\Value;

/**
 * Prevent saving of NULL values to the database.
 *
 * The `specificcountry` carrier configuration field must only save the
 * comma-separated list of country codes if specified. Saving an empty value,
 * especially in non-default scope, leads to undesired behaviour (setting
 * in global scope has no effect). This backend model can be used to prevent
 * saving of NULL values which happens when the allowed countries from the
 * general country options setting should be used (instead of the
 * carrier-specific list).
 */
class AllowedCountries extends Value
{
    /**
     * @return $this
     * @throws \Exception
     */
    #[\Override]
    public function save()
    {
        if (empty($this->getValue())) {
            return $this;
        }

        return parent::save();
    }
}
