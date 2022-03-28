<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment\Provider\Download;

use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;

abstract class AbstractUrl
{
    abstract public function getDownloadUrl(DocumentInterface $document, TrackInterface $track): string;
}
