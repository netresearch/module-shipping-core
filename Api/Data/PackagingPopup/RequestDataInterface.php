<?php

namespace Netresearch\ShippingCore\Api\Data\PackagingPopup;

/**
 * Data object containing normalized packaging popup request data.
 *
 * @api
 */
interface RequestDataInterface
{
    /**
     * Obtain package params.
     *
     * @return mixed[]
     */
    public function getPackages(): array;

    /**
     * Obtain item params.
     *
     * @return mixed[]
     */
    public function getShipmentItems(): array;

    /**
     * Get comment text for the shipment.
     *
     * @return string
     */
    public function getShipmentComment(): string;

    /**
     * Check if comment can be shown to customer.
     *
     * If true, comment will be visible in customer account and shipment confirmation email.
     *
     * @return true|null
     */
    public function isCommentNotificationEnabled(): ?bool;

    /**
     * Check if shipment confirmation email should be sent.
     *
     * @return true|null
     */
    public function isShipmentNotificationEnabled(): ?bool;
}
