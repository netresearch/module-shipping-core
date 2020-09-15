<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Config source model for the `showmethod` setting.
 */
class ShowIfNotApplicable implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => '0', 'label' => __('Hide this shipping method in checkout')],
            ['value' => '1', 'label' => __('Display customized message')],
        ];
    }
}
