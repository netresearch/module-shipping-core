<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentResponse;

use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ReturnShipmentDocumentInterface;

/**
 * An individually trackable shipping document.
 */
class ReturnShipmentDocument extends ShipmentDocument implements ReturnShipmentDocumentInterface
{
    /**
     * Get tracking number from response.
     *
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->getData(self::TRACKING_NUMBER);
    }
}
