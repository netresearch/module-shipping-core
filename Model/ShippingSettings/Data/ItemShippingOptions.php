<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface;

class ItemShippingOptions implements ItemShippingOptionsInterface
{
    /**
     * @var int
     */
    private $itemId = 0;

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    private $shippingOptions = [];

    /**
     * @return int
     */
    #[\Override]
    public function getItemId(): int
    {
        return $this->itemId;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    #[\Override]
    public function getShippingOptions(): array
    {
        return $this->shippingOptions;
    }

    /**
     * @param int $itemId
     *
     * @return void
     */
    #[\Override]
    public function setItemId(int $itemId): void
    {
        $this->itemId = $itemId;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[] $shippingOptions
     *
     * @return void
     */
    #[\Override]
    public function setShippingOptions(array $shippingOptions): void
    {
        $this->shippingOptions = $shippingOptions;
    }
}
