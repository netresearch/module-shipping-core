<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentResponse;

use Magento\Framework\DataObject;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentDocumentInterface;

/**
 * An individual document of a label response.
 *
 * The label response holds only a combined binary of all the returned shipment documents.
 */
class ShipmentDocument extends DataObject implements ShipmentDocumentInterface
{
    #[\Override]
    public function getTitle(): string
    {
        return $this->getData(self::TITLE);
    }

    #[\Override]
    public function getLabelData(): string
    {
        return $this->getData(self::LABEL_DATA);
    }

    #[\Override]
    public function getMimeType(): string
    {
        return $this->getData(self::MIME_TYPE);
    }
}
