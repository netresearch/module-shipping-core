<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection;

use Magento\Framework\Model\AbstractModel;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Netresearch\ShippingCore\Model\ResourceModel\Quote\Address\ShippingOptionSelection;

class QuoteSelection extends AbstractModel implements AssignedSelectionInterface
{
    /**
     * Initialize Quote Selection resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ShippingOptionSelection::class);
    }

    public function getParentId(): int
    {
        return (int) $this->getData(self::PARENT_ID);
    }

    public function getShippingOptionCode(): string
    {
        return (string) $this->getData(self::SHIPPING_OPTION_CODE);
    }

    public function setShippingOptionCode(string $shippingOptionCode): SelectionInterface
    {
        $this->setData(self::SHIPPING_OPTION_CODE, $shippingOptionCode);
        return $this;
    }

    public function getInputCode(): string
    {
        return (string) $this->getData(self::INPUT_CODE);
    }

    public function setInputCode(string $inputCode): SelectionInterface
    {
        $this->setData(self::INPUT_CODE, $inputCode);
        return $this;
    }

    public function getInputValue(): string
    {
        return (string) $this->getData(self::INPUT_VALUE);
    }

    public function setInputValue(string $inputValue): SelectionInterface
    {
        $this->setData(self::INPUT_VALUE, $inputValue);
        return $this;
    }
}
