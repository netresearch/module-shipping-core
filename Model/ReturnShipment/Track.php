<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment;

use Magento\Framework\Model\AbstractModel;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment\Track as TrackResource;

class Track extends AbstractModel implements TrackInterface
{
    /**
     * Initialize Track resource model.
     */
    protected function _construct()
    {
        $this->_init(TrackResource::class);
        parent::_construct();
    }

    public function getEntityId(): ?int
    {
        $entityId = $this->getData(self::ENTITY_ID);
        return $entityId ? (int) $entityId : null;
    }

    public function getOrderId(): int
    {
        return (int)$this->getData(self::ORDER_ID);
    }

    public function getCarrierCode(): string
    {
        return (string)$this->getData(self::CARRIER_CODE);
    }

    public function getTitle(): string
    {
        return (string)$this->getData(self::TITLE);
    }

    public function getTrackNumber(): string
    {
        return (string)$this->getData(self::TRACK_NUMBER);
    }

    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }
}
