<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment\Provider\Download;

use Magento\Framework\UrlInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;

class FrontendUrl extends AbstractUrl
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    #[\Override]
    public function getDownloadUrl(DocumentInterface $document, TrackInterface $track): string
    {
        return $this->urlBuilder->getUrl('nrshipping/rma/download', [
            'order_id' => $track->getOrderId(),
            'track_id' => $track->getEntityId(),
            'document_id' => $document->getId(),
        ]);
    }
}
