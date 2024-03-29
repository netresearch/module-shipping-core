<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption;

/**
 * Interface CompatibilityInterface
 *
 * @api
 */
interface CompatibilityInterface
{
    public const ACTION_HIDE = 'hide';
    public const ACTION_SHOW = 'show';
    public const ACTION_ENABLE = 'enable';
    public const ACTION_DISABLE = 'disable';
    public const ACTION_REQUIRE = 'require';
    public const ACTION_UNREQUIRE = 'unrequire';

    /**
     * The unique ID of the compatibility rule.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Returns a list of shipping option codes or compound codes ({shippingOptionCode}.{inputCode})
     * that should be affected by this rule.
     *
     * When not using a compound code to target a specific input, all inputs of the shipping option will be treated as
     * subjects.
     *
     * @return string[]
     */
    public function getSubjects(): array;

    /**
     * Error message to display if the compatibility rule is violated.
     * Will replace "%1" with a list of subjects.
     *
     * @example "The shipping options %1 require each other."
     * @example "Please choose only one of %1."
     *
     * @return string
     */
    public function getErrorMessage(): string;

    /**
     * Returns a list of shipping option codes or compund codes ({shippingOptionCode}.{inputCode})
     * that will influence the rule's subjects, but will never be disabled or hidden
     * by other subjects or masters (by this rule!). Use this to establish a hierarchy for
     * compatibility rules, e.g. one shipping option depending upon another.
     *
     * @example Given:
     *          $incompatibilityRule = false;
     *          $masters = ['shippingOption1'];
     *          $subjects = ['shippingOption2', 'shippingOption3'] = true;
     *      When nothing is selected, only 'shippingOption1' will be visible/enabled. When selecting 'shippingOption1',
     * shipping options
     *      'shippingOption2' and 'shippingOption3' will become visible/enabled, too.
     * @return string[]
     */
    public function getMasters(): array;

    /**
     * Returns the input value on which the rule should be triggered.
     * The default ("") causes the rule to be triggered on non-empty values.
     *
     * @return string
     */
    public function getTriggerValue(): string;

    /**
     * Will return one of ACTION_* public constants.
     * This decides what action to take if a master has the designated triggerValue.
     * If there is no match, the opposite action will be applied.
     *
     * @return string
     */
    public function getAction(): string;

    /**
     * @param string $id
     *
     * @return void
     */
    public function setId(string $id): void;

    /**
     * @param string[] $subjects
     *
     * @return void
     */
    public function setSubjects(array $subjects): void;

    /**
     * @param string $errorMessage
     *
     * @return void
     */
    public function setErrorMessage(string $errorMessage): void;

    /**
     * @param array $masters
     *
     * @return void
     */
    public function setMasters(array $masters): void;

    /**
     * @param string $triggerValue
     *
     * @return void
     */
    public function setTriggerValue(string $triggerValue): void;

    /**
     *
     * @param string $action
     * @return mixed
     */
    public function setAction(string $action);
}
