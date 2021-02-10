<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\PackagingPopup;

/**
 * Data object containing normalized packaging popup request data.
 */
class RequestData
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

    public function getPackages(): array
    {
        return $this->packages;
    }

    public function getShipmentItems(): array
    {
        return $this->shipmentItems;
    }

    /**
     * Get comment text for the shipment.
     *
     * @return string
     */
    public function getShipmentComment(): string
    {
        return $this->shipmentComment;
    }

    /**
     * Check if comment can be shown to customer.
     *
     * If true, comment will be visible in customer account and shipment confirmation email.
     *
     * @return true|null
     */
    public function isCommentNotificationEnabled(): ?bool
    {
        return $this->commentNotificationEnabled;
    }

    /**
     * Check if shipment confirmation email should be sent.
     *
     * @return true|null
     */
    public function isShipmentNotificationEnabled(): ?bool
    {
        return $this->shipmentNotificationEnabled;
    }
}
