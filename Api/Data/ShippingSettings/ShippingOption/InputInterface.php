<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption;

/**
 * Interface InputInterface
 *
 * @api
 */
interface InputInterface
{
    public const INPUT_TYPE_CHECKBOX = 'checkbox';
    public const INPUT_TYPE_LOCATION_FINDER = 'locationfinder';
    public const INPUT_TYPE_DATE = 'date';
    public const INPUT_TYPE_NUMBER = 'number';
    public const INPUT_TYPE_RADIO = 'radio';
    public const INPUT_TYPE_RADIOSET = 'radioset';
    public const INPUT_TYPE_SELECT = 'select';
    public const INPUT_TYPE_TEXT = 'text';
    public const INPUT_TYPE_TIME = 'time';

    /**
     * Get the display input type of the current shipping option input.
     *
     * @return string
     */
    public function getInputType(): string;

    /**
     * Get the unique identifier code for this input.
     *
     * The input code is only guaranteed to be unique among its parent shipping option's inputs.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Obtain the preconfigured value of a shipping option input.
     *
     * This must always be a string since in HTML inputs, only strings can be transferred as values.
     *
     * @return string
     */
    public function getDefaultValue(): string;

    /**
     * Declare if the input should be presented as read-only.
     *
     * @return bool
     */
    public function isDisabled(): bool;

    /**
     * Obtain the human-readable label corresponding to the input.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Declare if the label should be visibly rendered.
     *
     * @return bool
     */
    public function isLabelVisible(): bool;

    /**
     * Obtain a pre-defined set of allowed values, e.g for a select type input.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterface[]
     */
    public function getOptions(): array;

    /**
     * Obtain a help text to be displayed in a tooltip with the input.
     *
     * @return string
     */
    public function getTooltip(): string;

    /**
     * Obtain a placeholder text to be displayed when no value has been entered yet.
     *
     * @return string
     */
    public function getPlaceholder(): string;

    /**
     * Get sort order of the input among its siblings.
     *
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * Get a list of rules for user input validation during runtime. For a list of mapped rules see:
     *
     * @file view/frontend/web/js/model/service-validation-map.js
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValidationRuleInterface[]
     */
    public function getValidationRules(): array;

    /**
     * Retrieve an optional comment to be displayed with the input.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface|null
     */
    public function getComment(): ?CommentInterface;

    /**
     * Retrieve an optional combination rule for this input.
     * This only works on package level inputs in the context of the packaging popup.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemCombinationRuleInterface|null
     */
    public function getItemCombinationRule(): ?ItemCombinationRuleInterface;

    /**
     * Retrieve a list of mappings of input values to "input code" => "value maps"
     *
     * This can is used to let this input directly change the values of other inputs,
     * for example updating the package dimensions when selecting a package type.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterface[]
     */
    public function getValueMaps(): array;

    /**
     * @param string $inputType
     *
     * @return void
     */
    public function setInputType(string $inputType): void;

    /**
     * @param string $code
     *
     * @return void
     */
    public function setCode(string $code): void;

    /**
     * @param string $defaultValue
     *
     * @return void
     */
    public function setDefaultValue(string $defaultValue): void;

    /**
     * @param bool $disabled
     *
     * @return void
     */
    public function setDisabled(bool $disabled): void;

    /**
     * @param string $label
     *
     * @return void
     */
    public function setLabel(string $label): void;

    /**
     * @param bool $labelVisible
     *
     * @return void
     */
    public function setLabelVisible(bool $labelVisible): void;

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterface[] $options
     *
     * @return void
     */
    public function setOptions(array $options): void;

    /**
     * @param string $tooltip
     *
     * @return void
     */
    public function setTooltip(string $tooltip): void;

    /**
     * @param string $placeholder
     *
     * @return void
     */
    public function setPlaceholder(string $placeholder): void;

    /**
     * @param int $sortOrder
     *
     * @return void
     */
    public function setSortOrder(int $sortOrder): void;

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValidationRuleInterface[] $validationRules
     *
     * @return void
     */
    public function setValidationRules(array $validationRules): void;

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface|null $comment
     *
     * @return void
     */
    public function setComment($comment): void;

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemCombinationRuleInterface|null $itemCombinationRule
     *
     * @return void
     */
    public function setItemCombinationRule($itemCombinationRule): void;

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterface[] $valueMaps
     *
     * @return void
     */
    public function setValueMaps(array $valueMaps): void;
}
