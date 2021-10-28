<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment;

use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentLinkInterface;

class DocumentLink implements DocumentLinkInterface
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $url;

    public function __construct(string $title, string $url)
    {
        $this->title = $title;
        $this->url = $url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
