<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\SplitAddress\SplittingRule;

use Magento\Sales\Api\Data\OrderAddressInterface;
use Netresearch\ShippingCore\Api\Data\RecipientStreetInterface;
use Netresearch\ShippingCore\Api\SplitAddress\SplittingRuleInterface;

class CompositeRule implements SplittingRuleInterface
{
    /**
     * @var SplittingRuleInterface[]
     */
    private $rules;

    /**
     * @param SplittingRuleInterface[] $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * Apply all registered rules.
     *
     * @param OrderAddressInterface $address
     * @param RecipientStreetInterface $recipientStreet
     * @return void
     */
    #[\Override]
    public function apply(OrderAddressInterface $address, RecipientStreetInterface $recipientStreet): void
    {
        foreach ($this->rules as $rule) {
            $rule->apply($address, $recipientStreet);
        }
    }
}
