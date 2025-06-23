<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config\Backend;

use Magento\Framework\App\Config\Data\ProcessorInterface;
use Netresearch\ShippingCore\Model\Config\ParcelProcessingConfig;

/**
 * Handle the `is_default` radio group before saving the serialized data.
 */
class PackageParams extends ArraySerialized
{
    /**
     * Move `is_default` setting to proper row
     *
     * @return ArraySerialized
     */
    #[\Override]
    public function beforeSave()
    {
        $key = ParcelProcessingConfig::CONFIG_FIELD_PACKAGE_IS_DEFAULT;

        $value = $this->getValue();
        if (\is_array($value) && isset($value[$key])) {
            $defaultPackageId = $value[$key];
            unset($value[$key]);
            $value[$defaultPackageId][$key] = true;
        }
        $this->setValue($value);

        return parent::beforeSave();
    }
}
