<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\OrderExport\Data;

use Netresearch\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface;

class KeyValueObject implements KeyValueObjectInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string|float|boolean|integer
     */
    private $value;

    /**
     * KeyValueObject constructor.
     *
     * @param string $key
     * @param bool|float|int|string $value
     */
    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    #[\Override]
    public function getKey(): string
    {
        return $this->key;
    }

    #[\Override]
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return bool|float|int|string
     */
    #[\Override]
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param bool|float|int|string $value
     * @return $this
     */
    #[\Override]
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
