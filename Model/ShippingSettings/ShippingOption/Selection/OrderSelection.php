<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection;

use Magento\Framework\Model\AbstractModel;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Netresearch\ShippingCore\Model\ResourceModel\Order\Address\ShippingOptionSelection;

class OrderSelection extends AbstractModel implements AssignedSelectionInterface
{
    /**
     * Initialize Order Selection resource model
     */
    #[\Override]
    protected function _construct()
    {
        $this->_init(ShippingOptionSelection::class);
        parent::_construct();
    }

    #[\Override]
    public function getParentId(): int
    {
        return (int) $this->getData(self::PARENT_ID);
    }

    #[\Override]
    public function getShippingOptionCode(): string
    {
        return (string) $this->getData(self::SHIPPING_OPTION_CODE);
    }

    #[\Override]
    public function setShippingOptionCode(string $shippingOptionCode): SelectionInterface
    {
        $this->setData(self::SHIPPING_OPTION_CODE, $shippingOptionCode);
        return $this;
    }

    #[\Override]
    public function getInputCode(): string
    {
        return (string) $this->getData(self::INPUT_CODE);
    }

    #[\Override]
    public function setInputCode(string $inputCode): SelectionInterface
    {
        $this->setData(self::INPUT_CODE, $inputCode);
        return $this;
    }

    #[\Override]
    public function getInputValue(): string
    {
        return (string) $this->getData(self::INPUT_VALUE);
    }

    #[\Override]
    public function setInputValue(string $inputValue): SelectionInterface
    {
        $this->setData(self::INPUT_VALUE, $inputValue);
        return $this;
    }
}
