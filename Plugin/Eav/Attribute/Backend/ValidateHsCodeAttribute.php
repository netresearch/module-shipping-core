<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\Eav\Attribute\Backend;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;
use Netresearch\ShippingCore\Setup\Module\DataInstaller;

class ValidateHsCodeAttribute
{
    /**
     * @param AbstractBackend $backendModel
     * @param bool|mixed $result
     * @param mixed $eavEntity
     * @return bool|mixed
     * @throws LocalizedException
     */
    public function afterValidate(AbstractBackend $backendModel, $result, $eavEntity)
    {
        if (!$eavEntity instanceof Product) {
            return $result;
        }

        $attrCode = $backendModel->getAttribute()->getAttributeCode();
        if ($attrCode !== DataInstaller::ATTRIBUTE_CODE_HS_CODE) {
            return $result;
        }

        $value = $eavEntity->getData($attrCode);
        if (!$value) {
            return $result;
        }

        $label = $backendModel->getAttribute()->getData('frontend_label');

        if (!is_numeric($value)) {
            throw new LocalizedException(__('The value of attribute "%1" must be numeric.', $label));
        }

        // only allow digits that have a length of 6, 8 or 10.
        if (!\in_array(strlen((string) $value), [6, 8, 10], true)) {
            throw new LocalizedException(
                __('The value of attribute "%1" must be either 6, 8 or 10 digits long.', $label)
            );
        }

        return $result;
    }
}
