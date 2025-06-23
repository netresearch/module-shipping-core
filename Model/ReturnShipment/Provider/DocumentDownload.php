<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment\Provider;

use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentDownloadInterface;
use Netresearch\ShippingCore\Model\ReturnShipment\Provider\Download\AbstractUrl;

class DocumentDownload implements DocumentDownloadInterface
{
    /**
     * @var AbstractUrl
     */
    private $urlBuilder;

    public function __construct(AbstractUrl $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    #[\Override]
    public function getUrl(DocumentInterface $document, TrackInterface $track): string
    {
        return $this->urlBuilder->getDownloadUrl($document, $track);
    }

    #[\Override]
    public function getFileName(DocumentInterface $document, TrackInterface $track, OrderInterface $order): string
    {
        $fileExt = match ($document->getMediaType()) {
            'application/pdf' => 'pdf',
            'image/png' => 'png',
            default => throw new \RuntimeException('File extension for ' . $document->getMediaType() . ' is not defined.'),
        };

        $filename = sprintf(
            '%s-%s-(%s)-%s.%s',
            $order->getStore()->getFrontendName(),
            $order->getRealOrderId(),
            $track->getTrackNumber(),
            $document->getTitle(),
            $fileExt
        );

        return strtolower(str_replace(' ', '_', $filename));
    }
}
