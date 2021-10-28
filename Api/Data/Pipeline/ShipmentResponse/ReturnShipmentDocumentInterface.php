<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse;

/**
 * A special document of a positive (multi-)label response that comes with its own tracking number.
 *
 * @api
 */
interface ReturnShipmentDocumentInterface extends ShipmentDocumentInterface
{
    public const TRACKING_NUMBER = 'tracking_number';

    /**
     * @return string
     */
    public function getTrackingNumber(): string;
}
