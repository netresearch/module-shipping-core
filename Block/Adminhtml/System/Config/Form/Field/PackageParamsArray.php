<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Netresearch\ShippingCore\Model\Config\ParcelProcessingConfig;

class PackageParamsArray extends AbstractFieldArray
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var PackageDefault
     */
    private $packageDefaultRenderer;

    public function __construct(
        Context $context,
        ScopeConfigInterface $config,
        PackageDefault $packageDefaultRenderer,
        array $data = []
    ) {
        $this->config = $config;
        $this->packageDefaultRenderer = $packageDefaultRenderer;

        parent::__construct($context, $data);
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            ParcelProcessingConfig::CONFIG_FIELD_PACKAGE_TITLE,
            [
                'label' => __('Title'),
                'style' => 'width:100px',
                'class' => 'required',
            ]
        );
        $this->addColumn(
            ParcelProcessingConfig::CONFIG_FIELD_PACKAGE_LENGTH,
            [
                'label' => __('Length %1', $this->getMeasureLengthUnit()),
                'style' => 'width:40px',
                'class' => 'validate-digits required',
            ]
        );
        $this->addColumn(
            ParcelProcessingConfig::CONFIG_FIELD_PACKAGE_WIDTH,
            [
                'label' => __('Width %1', $this->getMeasureLengthUnit()),
                'style' => 'width:40px',
                'class' => 'validate-digits required',
            ]
        );
        $this->addColumn(
            ParcelProcessingConfig::CONFIG_FIELD_PACKAGE_HEIGHT,
            [
                'label' => __('Height %1', $this->getMeasureLengthUnit()),
                'style' => 'width:40px',
                'class' => 'validate-number required',
            ]
        );

        $this->addColumn(
            ParcelProcessingConfig::CONFIG_FIELD_PACKAGE_WEIGHT,
            [
                'label' => __('Weight %1', $this->getWeightUnit()),
                'style' => 'width:40px',
                'class' => 'validate-number required',
            ]
        );

        $this->addColumn(
            ParcelProcessingConfig::CONFIG_FIELD_PACKAGE_IS_DEFAULT,
            [
                'label' => __('Set Default'),
                'renderer' => $this->packageDefaultRenderer,
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Package');
    }

    private function getWeightUnit(): string
    {
        /** @var AbstractElement $element */
        $element = $this->getData('element');
        $scope = (string) $element->getData('scope');
        $scopeId = (int) $element->getData('scope_id') ?: 0;

        return (string) $this->config->getValue(Data::XML_PATH_WEIGHT_UNIT, $scope, $scopeId);
    }

    private function getMeasureLengthUnit(): string
    {
        return $this->getWeightUnit() === 'kgs' ? 'cm' : 'inch';
    }
}
