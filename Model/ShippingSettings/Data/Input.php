<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemCombinationRuleInterface;

class Input implements InputInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $inputType = 'text';

    /**
     * @var string
     */
    private $defaultValue = '';

    /**
     * @var bool
     */
    private $disabled = false;

    /**
     * @var string
     */
    private $label = '';

    /**
     * @var bool
     */
    private $labelVisible = true;

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterface[]
     */
    private $options = [];

    /**
     * @var string
     */
    private $tooltip = '';

    /**
     * @var string
     */
    private $placeholder = '';

    /**
     * @var int
     */
    private $sortOrder = 0;

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValidationRuleInterface[]
     */
    private $validationRules = [];

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface|null
     */
    private $comment;

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemCombinationRuleInterface|null
     */
    private $itemCombinationRule;

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterface[]
     */
    private $valueMaps = [];

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getInputType(): string
    {
        return $this->inputType;
    }

    /**
     * @return string
     */
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isLabelVisible(): bool
    {
        return $this->labelVisible;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterface[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getTooltip(): string
    {
        return $this->tooltip;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValidationRuleInterface[]
     */
    public function getValidationRules(): array
    {
        return $this->validationRules;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface|null
     */
    public function getComment(): ?CommentInterface
    {
        return $this->comment;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemCombinationRuleInterface|null
     */
    public function getItemCombinationRule(): ?ItemCombinationRuleInterface
    {
        return $this->itemCombinationRule;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterface[]
     */
    public function getValueMaps(): array
    {
        return $this->valueMaps;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @param string $inputType
     */
    public function setInputType(string $inputType): void
    {
        $this->inputType = $inputType;
    }

    /**
     * @param string $defaultValue
     */
    public function setDefaultValue(string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @param bool $disabled
     */
    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @param bool $labelVisible
     */
    public function setLabelVisible(bool $labelVisible): void
    {
        $this->labelVisible = $labelVisible;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterface[] $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param string $tooltip
     */
    public function setTooltip(string $tooltip): void
    {
        $this->tooltip = $tooltip;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @param int $sortOrder
     */
    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValidationRuleInterface[] $validationRules
     */
    public function setValidationRules(array $validationRules): void
    {
        $this->validationRules = $validationRules;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface|null $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemCombinationRuleInterface|null $itemCombinationRule
     */
    public function setItemCombinationRule($itemCombinationRule): void
    {
        $this->itemCombinationRule = $itemCombinationRule;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterface[] $valueMaps
     */
    public function setValueMaps(array $valueMaps): void
    {
        $this->valueMaps = $valueMaps;
    }
}
