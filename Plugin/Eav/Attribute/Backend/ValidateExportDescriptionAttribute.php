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

class ValidateExportDescriptionAttribute
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
        if ($attrCode !== DataInstaller::ATTRIBUTE_CODE_EXPORT_DESCRIPTION) {
            return $result;
        }

        $value = $eavEntity->getData($attrCode);
        if (!$value) {
            return $result;
        }

        if (strlen((string) $value) > 50) {
            $label = $backendModel->getAttribute()->getData('frontend_label');
            throw new LocalizedException(
                __('The value of attribute "%1" must not be longer than %2 characters.', $label, 50)
            );
        }

        return $result;
    }
}
