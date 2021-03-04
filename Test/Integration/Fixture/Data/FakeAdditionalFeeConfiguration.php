<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ShippingCore\Test\Integration\Fixture\Data;

use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote;
use Netresearch\ShippingCore\Api\AdditionalFee\AdditionalFeeConfigurationInterface;

class FakeAdditionalFeeConfiguration implements AdditionalFeeConfigurationInterface
{
    const CARRIERCODE = 'testcarrier';

    const LABEL = 'testlabel';

    const CHARGE = 22.22;

    public function getCarrierCode(): string
    {
        return self::CARRIERCODE;
    }

    public function isActive(Quote $quote): bool
    {
        return true;
    }

    public function getServiceCharge(Quote $quote): float
    {
        return self::CHARGE;
    }

    public function getLabel(): Phrase
    {
        return __(self::LABEL);
    }
}
