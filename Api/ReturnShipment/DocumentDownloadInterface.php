<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ReturnShipment;

use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;

/**
 * Provide file name and download URL for a return shipment document.
 *
 * @api
 */
interface DocumentDownloadInterface
{
    public function getUrl(DocumentInterface $document, TrackInterface $track): string;

    public function getFileName(DocumentInterface $document, TrackInterface $track, OrderInterface $order): string;
}
