<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemCombinationRuleInterface;

class ItemCombinationRule implements ItemCombinationRuleInterface
{
    /**
     * @var string
     */
    private $sourceItemInputCode;

    /**
     * @var string[]
     */
    private $additionalSourceInputCodes = [];

    /**
     * @var string
     */
    private $action;

    /**
     * @return string
     */
    public function getSourceItemInputCode(): string
    {
        return $this->sourceItemInputCode;
    }

    /**
     * @return string[]
     */
    public function getAdditionalSourceInputCodes(): array
    {
        return $this->additionalSourceInputCodes;
    }

    /**
     * @param string $sourceItemInputCode
     */
    public function setSourceItemInputCode(string $sourceItemInputCode): void
    {
        $this->sourceItemInputCode = $sourceItemInputCode;
    }

    /**
     * @param string[] $additionalServiceInputCodes
     */
    public function setAdditionalSourceInputCodes(array $additionalServiceInputCodes)
    {
        $this->additionalSourceInputCodes = $additionalServiceInputCodes;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }
}
