<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ExportContentType implements OptionSourceInterface
{
    private const TYPE_COMMERCIAL_GOODS = 'COMMERCIAL_GOODS';
    private const TYPE_COMMERCIAL_SAMPLE = 'COMMERCIAL_SAMPLE';
    private const TYPE_DOCUMENT = 'DOCUMENT';
    private const TYPE_PRESENT = 'PRESENT';
    private const TYPE_RETURN_OF_GOODS = 'RETURN_OF_GOODS';
    private const TYPE_OTHER = 'OTHER';

    /**
     * Options getter
     *
     * @return mixed[]
     */
    public function toOptionArray()
    {
        $optionArray = [];

        $options = $this->toArray();
        foreach ($options as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }

        return $optionArray;
    }

    /**
     * Get options
     *
     * @return mixed[]
     */
    public function toArray()
    {
        return [
            self::TYPE_COMMERCIAL_GOODS => __('Commercial Goods'),
            self::TYPE_COMMERCIAL_SAMPLE => __('Commercial Sample'),
            self::TYPE_DOCUMENT => __('Document'),
            self::TYPE_PRESENT => __('Present'),
            self::TYPE_RETURN_OF_GOODS => __('Return of Goods'),
            self::TYPE_OTHER => __('Other'),
        ];
    }
}
