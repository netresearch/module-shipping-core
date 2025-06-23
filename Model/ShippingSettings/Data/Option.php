<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterface;

class Option implements OptionInterface
{
    /**
     * @var string
     */
    private $id = '';

    /**
     * @var string
     */
    private $label = '';

    /**
     * @var string
     */
    private $value = '';

    /**
     * @var bool
     */
    private $disabled = false;

    /**
     * @return string
     */
    #[\Override]
    public function getId(): string
    {
        return $this->id;
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
     * @return string
     */
    #[\Override]
    public function getValue(): string
    {
        return $this->value;
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
     * @param string $id
     */
    #[\Override]
    public function setId(string $id): void
    {
        $this->id = $id;
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
     * @param string $value
     */
    #[\Override]
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @param bool $disabled
     */
    #[\Override]
    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
