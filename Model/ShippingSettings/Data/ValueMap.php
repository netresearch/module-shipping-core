<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterface;

/**
 * Maps a source input value to a list of "input code" => "value" maps
 *
 * This can is used to let an input directly change the values of other inputs,
 * for example updating the package dimensions when selecting a package type.
 */
class ValueMap implements ValueMapInterface
{
    /**
     * @var string
     */
    private $sourceValue;

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap\InputValueInterface[]
     */
    private $inputValues = [];

    /**
     * @return string
     */
    #[\Override]
    public function getSourceValue(): string
    {
        return $this->sourceValue;
    }

    /**
     * @param string $sourceValue
     */
    #[\Override]
    public function setSourceValue(string $sourceValue): void
    {
        $this->sourceValue = $sourceValue;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap\InputValueInterface[]
     */
    #[\Override]
    public function getInputValues(): array
    {
        return $this->inputValues;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap\InputValueInterface[] $inputValues
     */
    #[\Override]
    public function setInputValues(array $inputValues): void
    {
        $this->inputValues = $inputValues;
    }
}
