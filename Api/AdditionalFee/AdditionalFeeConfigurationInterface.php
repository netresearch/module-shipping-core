<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\AdditionalFee;

use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote;

/**
 * Configure total adjustment.
 *
 * Implementations announce whether or not to apply a custom total, which amount, how it should be named.
 *
 * @api
 */
interface AdditionalFeeConfigurationInterface
{
    public function getCarrierCode(): string;

    public function getLabel(): Phrase;

    public function isActive(Quote $quote): bool;

    public function getServiceCharge(Quote $quote): float;
}
