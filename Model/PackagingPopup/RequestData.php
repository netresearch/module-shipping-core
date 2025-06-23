<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\PackagingPopup;

use Netresearch\ShippingCore\Api\Data\PackagingPopup\RequestDataInterface;

class RequestData implements RequestDataInterface
{
    /**
     * @var mixed[]
     */
    private $packages;

    /**
     * @var int[]
     */
    private $shipmentItems;

    /**
     * @var string
     */
    private $shipmentComment;

    /**
     * @var true|null
     */
    private $commentNotificationEnabled;

    /**
     * @var true|null
     */
    private $shipmentNotificationEnabled;

    public function __construct(
        array $packages,
        array $shipmentItems,
        string $shipmentComment,
        ?bool $commentNotificationEnabled,
        ?bool $shipmentNotificationEnabled
    ) {
        $this->packages = $packages;
        $this->shipmentItems = $shipmentItems;
        $this->shipmentComment = $shipmentComment;
        $this->commentNotificationEnabled = $commentNotificationEnabled;
        $this->shipmentNotificationEnabled = $shipmentNotificationEnabled;
    }

    #[\Override]
    public function getPackages(): array
    {
        return $this->packages;
    }

    #[\Override]
    public function getShipmentItems(): array
    {
        return $this->shipmentItems;
    }

    #[\Override]
    public function getShipmentComment(): string
    {
        return $this->shipmentComment;
    }

    #[\Override]
    public function isCommentNotificationEnabled(): ?bool
    {
        return $this->commentNotificationEnabled;
    }

    #[\Override]
    public function isShipmentNotificationEnabled(): ?bool
    {
        return $this->shipmentNotificationEnabled;
    }
}
