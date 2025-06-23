<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment;

use Magento\Framework\Model\AbstractModel;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment\Document as DocumentResource;

class Document extends AbstractModel implements DocumentInterface
{
    /**
     * Initialize Document resource model.
     */
    #[\Override]
    protected function _construct()
    {
        $this->_init(DocumentResource::class);
        parent::_construct();
    }

    #[\Override]
    public function getEntityId(): ?int
    {
        $entityId = $this->getData(self::ENTITY_ID);
        return $entityId ? (int) $entityId : null;
    }

    #[\Override]
    public function getTrackId(): int
    {
        return (int)$this->getData(self::TRACK_ID);
    }

    #[\Override]
    public function getTitle(): string
    {
        return (string)$this->getData(self::TITLE);
    }

    #[\Override]
    public function getLabelData(): string
    {
        return (string)$this->getData(self::LABEL_DATA);
    }

    #[\Override]
    public function getMediaType(): string
    {
        return (string)$this->getData(self::MEDIA_TYPE);
    }

    #[\Override]
    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }
}
