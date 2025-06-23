<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ItemAttribute;

use Magento\Framework\Model\AbstractModel;
use Netresearch\ShippingCore\Api\Data\OrderItemAttributesInterface;
use Netresearch\ShippingCore\Model\ResourceModel\OrderItemAttributes as OrderItemAttributesResource;

class OrderItemAttributes extends AbstractModel implements OrderItemAttributesInterface
{
    /**
     * Initialize OrderItemAttributes resource model.
     */
    #[\Override]
    protected function _construct()
    {
        $this->_init(OrderItemAttributesResource::class);
        parent::_construct();
    }

    /**
     * @return int
     */
    #[\Override]
    public function getItemId(): int
    {
        return (int) $this->getData(self::ITEM_ID);
    }

    /**
     * @return string
     */
    #[\Override]
    public function getHsCode(): string
    {
        return (string) $this->getData(self::HS_CODE);
    }

    /**
     * @return string
     */
    #[\Override]
    public function getCountryOfManufacture(): string
    {
        return (string) $this->getData(self::COUNTRY_OF_MANUFACTURE);
    }

    /**
     * @param int $itemId
     */
    #[\Override]
    public function setItemId(int $itemId): void
    {
        $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * @param string|null $hsCode
     */
    #[\Override]
    public function setHsCode(?string $hsCode = null): void
    {
        $this->setData(self::HS_CODE, $hsCode);
    }

    /**
     * @param string|null $countryOfManufacture
     */
    #[\Override]
    public function setCountryOfManufacture(?string $countryOfManufacture = null): void
    {
        $this->setData(self::COUNTRY_OF_MANUFACTURE, $countryOfManufacture);
    }
}
