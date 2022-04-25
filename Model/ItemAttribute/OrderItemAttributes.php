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
    protected function _construct()
    {
        $this->_init(OrderItemAttributesResource::class);
        parent::_construct();
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return (int) $this->getData(self::ITEM_ID);
    }

    /**
     * @return string
     */
    public function getHsCode(): string
    {
        return (string) $this->getData(self::HS_CODE);
    }

    /**
     * @return string
     */
    public function getCountryOfManufacture(): string
    {
        return (string) $this->getData(self::COUNTRY_OF_MANUFACTURE);
    }

    /**
     * @param int $itemId
     */
    public function setItemId(int $itemId): void
    {
        $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * @param string|null $hsCode
     */
    public function setHsCode(string $hsCode = null): void
    {
        $this->setData(self::HS_CODE, $hsCode);
    }

    /**
     * @param string|null $countryOfManufacture
     */
    public function setCountryOfManufacture(string $countryOfManufacture = null): void
    {
        $this->setData(self::COUNTRY_OF_MANUFACTURE, $countryOfManufacture);
    }
}
