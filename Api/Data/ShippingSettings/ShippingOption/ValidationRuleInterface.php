<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption;

/**
 * Interface ValidationRuleInterface
 *
 * A DTO to represent a validation rule to be used at runtime to validate user input.
 *
 * @api
 */
interface ValidationRuleInterface
{
    /**
     * Name of the validation rule.
     *
     * @file netresearch/module-shipping-ui/view/base/web/js/mixin/validator.js for custom validation rules.
     * @file magento/module-ui/view/base/web/js/lib/validation/rules.js for core validation rules
     * @return string
     */
    public function getName(): string;

    /**
     * Parameter available to the validation rule (eg max number of characters)
     *
     * @return mixed|null
     */
    public function getParam();

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @param mixed $param
     *
     * @return void
     */
    public function setParam($param): void;
}
