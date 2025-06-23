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
    #[\Override]
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getInputType(): string
    {
        return $this->inputType;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    #[\Override]
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    #[\Override]
    public function isLabelVisible(): bool
    {
        return $this->labelVisible;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterface[]
     */
    #[\Override]
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getTooltip(): string
    {
        return $this->tooltip;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @return int
     */
    #[\Override]
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValidationRuleInterface[]
     */
    #[\Override]
    public function getValidationRules(): array
    {
        return $this->validationRules;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface|null
     */
    #[\Override]
    public function getComment(): ?CommentInterface
    {
        return $this->comment;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemCombinationRuleInterface|null
     */
    #[\Override]
    public function getItemCombinationRule(): ?ItemCombinationRuleInterface
    {
        return $this->itemCombinationRule;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterface[]
     */
    #[\Override]
    public function getValueMaps(): array
    {
        return $this->valueMaps;
    }

    /**
     * @param string $code
     */
    #[\Override]
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @param string $inputType
     */
    #[\Override]
    public function setInputType(string $inputType): void
    {
        $this->inputType = $inputType;
    }

    /**
     * @param string $defaultValue
     */
    #[\Override]
    public function setDefaultValue(string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @param bool $disabled
     */
    #[\Override]
    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }

    /**
     * @param string $label
     */
    #[\Override]
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @param bool $labelVisible
     */
    #[\Override]
    public function setLabelVisible(bool $labelVisible): void
    {
        $this->labelVisible = $labelVisible;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterface[] $options
     */
    #[\Override]
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param string $tooltip
     */
    #[\Override]
    public function setTooltip(string $tooltip): void
    {
        $this->tooltip = $tooltip;
    }

    /**
     * @param string $placeholder
     */
    #[\Override]
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @param int $sortOrder
     */
    #[\Override]
    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValidationRuleInterface[] $validationRules
     */
    #[\Override]
    public function setValidationRules(array $validationRules): void
    {
        $this->validationRules = $validationRules;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface|null $comment
     */
    #[\Override]
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemCombinationRuleInterface|null $itemCombinationRule
     */
    #[\Override]
    public function setItemCombinationRule($itemCombinationRule): void
    {
        $this->itemCombinationRule = $itemCombinationRule;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterface[] $valueMaps
     */
    #[\Override]
    public function setValueMaps(array $valueMaps): void
    {
        $this->valueMaps = $valueMaps;
    }
}
