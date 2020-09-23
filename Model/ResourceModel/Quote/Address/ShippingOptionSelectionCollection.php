<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\Quote\Address;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netresearch\ShippingCore\Model\ResourceModel\Quote\Address\ShippingOptionSelection as ShippingOptionSelectionResource;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelection;

class ShippingOptionSelectionCollection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(QuoteSelection::class, ShippingOptionSelectionResource::class);
    }
}
