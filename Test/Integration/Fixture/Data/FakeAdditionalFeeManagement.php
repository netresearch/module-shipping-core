<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ShippingCore\Test\Integration\Fixture\Data;

use Netresearch\ShippingCore\Model\AdditionalFee\AdditionalFeeManagement;

class FakeAdditionalFeeManagement extends AdditionalFeeManagement
{
    public function __construct($additionalFeeConfiguration = [])
    {
        $additionalFeeConfiguration[] = new FakeAdditionalFeeConfiguration();
        parent::__construct($additionalFeeConfiguration);
    }
}
