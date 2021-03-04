<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\SplitAddress;

use Magento\Framework\Model\AbstractModel;
use Netresearch\ShippingCore\Api\Data\RecipientStreetInterface;
use Netresearch\ShippingCore\Model\ResourceModel\RecipientStreet as RecipientStreetResource;

class RecipientStreet extends AbstractModel implements RecipientStreetInterface
{
    /**
     * Initialize RecipientStreet resource model.
     */
    protected function _construct()
    {
        $this->_init(RecipientStreetResource::class);
        parent::_construct();
    }

    /**
     * Get the order address id.
     *
     * @return int|null
     */
    public function getOrderAddressId(): ?int
    {
        return $this->hasData(self::ORDER_ADDRESS_ID) ? (int) $this->getData(self::ORDER_ADDRESS_ID) : null;
    }

    /**
     * Get street name.
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    /**
     * Get street number.
     *
     * @return string
     */
    public function getNumber(): string
    {
        return (string) $this->getData(self::NUMBER);
    }

    /**
     * Get supplement.
     *
     * @return string
     */
    public function getSupplement(): string
    {
        return (string) $this->getData(self::SUPPLEMENT);
    }
}
