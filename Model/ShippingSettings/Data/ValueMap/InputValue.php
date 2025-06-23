<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data\ValueMap;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap\InputValueInterface;

/**
 * A map of an input code to a value.
 */
class InputValue implements InputValueInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    #[\Override]
    public function getCode(): string
    {
        return $this->code;
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
     * @return string
     */
    #[\Override]
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    #[\Override]
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
