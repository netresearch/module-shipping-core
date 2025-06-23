<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\View\Element\AbstractBlock;

class PackageDefault extends AbstractBlock
{
    /**
     * @param string $value
     *
     * @return self
     */
    public function setInputName(string $value): AbstractBlock
    {
        return $this->setData('name', $value);
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public function setInputId(string $value): AbstractBlock
    {
        return $this->setData('id', $value);
    }

    /**
     * Render the "is default" radio button.
     *
     * @return string
     */
    #[\Override]
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $html = '<input type="radio" value="%s" id="%s" name="%s" class="input-radio">';

        // remove package id from element name to realize a radio group
        $name = str_replace('[<%- _id %>]', '', $this->getData('name'));
        return sprintf($html, '<%- _id %>', $this->getData('id'), $name);
    }
}
