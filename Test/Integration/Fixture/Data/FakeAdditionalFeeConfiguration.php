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
    public const CARRIERCODE = 'testcarrier';

    public const LABEL = 'testlabel';

    public const CHARGE = 22.22;

    #[\Override]
    public function getCarrierCode(): string
    {
        return self::CARRIERCODE;
    }

    #[\Override]
    public function isActive(Quote $quote): bool
    {
        return true;
    }

    #[\Override]
    public function getServiceCharge(Quote $quote): float
    {
        return self::CHARGE;
    }

    #[\Override]
    public function getLabel(): Phrase
    {
        return __(self::LABEL);
    }
}
