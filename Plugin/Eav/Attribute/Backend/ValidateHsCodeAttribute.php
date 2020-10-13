<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\Eav\Attribute\Backend;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;

class ValidateHsCodeAttribute
{
    /**
     * @param AbstractBackend $backendModel
     * @param bool $result
     * @param mixed $eavEntity
     * @return bool
     * @throws LocalizedException
     */
    public function afterValidate(AbstractBackend $backendModel, bool $result, $eavEntity)
    {
        if (!$eavEntity instanceof Product) {
            return $result;
        }

        $attrCode = $backendModel->getAttribute()->getAttributeCode();
        if ($attrCode !== 'nrshipping_hs_code') {
            return $result;
        }

        $value = $eavEntity->getData($attrCode);
        $label = $backendModel->getAttribute()->getData('frontend_label');

        if (!empty($value) && !is_numeric($value)) {
            throw new LocalizedException(__('The value of attribute "%1" must be numeric.', $label));
        }

        $maxLength = 11;
        if (strlen((string) $value) > $maxLength) {
            throw new LocalizedException(
                __('The value of attribute "%1" must not be longer than %2 characters.', $label, $maxLength)
            );
        }

        return $result;
    }
}
