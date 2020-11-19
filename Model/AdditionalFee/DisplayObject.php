<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\AdditionalFee;

use Magento\Framework\DataObject;

/**
 * Bundle data for display purposes
 */
class DisplayObject extends DataObject
{
    public function getValueInclTax(): float
    {
        return (float) $this->getData('value_incl_tax');
    }

    public function getValueExclTax(): float
    {
        return (float) $this->getData('value');
    }

    public function getBaseValueInclTax(): float
    {
        return (float) $this->getData('base_value_incl_tax');
    }

    public function getBaseValueExclTax(): float
    {
        return (float) $this->getData('base_value');
    }

    public function getLabel(): string
    {
        return (string) $this->getData('label');
    }
}
