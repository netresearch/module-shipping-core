<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap;

/**
 * Interface InputValueInterface
 *
 * A Map of an input code to a value.
 *
 * @api
 */
interface InputValueInterface
{
    /**
     * Get the compound code (<optionCode>.<inputCode>) of
     * the target input component to manipulate.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Get the value to apply to the target input component.
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * @param string $code
     *
     * @return void
     */
    public function setCode(string $code): void;

    /**
     * @param string $value
     *
     * @return void
     */
    public function setValue(string $value): void;
}
