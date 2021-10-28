<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ReturnShipment;

use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentLinkInterface;

/**
 * @api
 */
interface GetDocumentLinksInterface
{
    /**
     * @param int $trackId
     * @return DocumentLinkInterface[]
     */
    public function execute(int $trackId): array;
}
