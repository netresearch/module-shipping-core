<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse;

/**
 * A positive label response with combined binary label as well as individual documents.
 *
 * @api
 */
interface LabelResponseInterface extends ShipmentResponseInterface
{
    public const TRACKING_NUMBER = 'tracking_number';
    public const SHIPPING_LABEL_CONTENT = 'shipping_label_content';
    public const DOCUMENTS = 'documents';

    /**
     * Get tracking number.
     *
     * @return string
     */
    public function getTrackingNumber(): string;

    /**
     * Get merged binaries of all label documents.
     *
     * @return string
     */
    public function getShippingLabelContent(): string;

    /**
     * Get individual documents created for the shipment, e.g. shipping label, customs form, return label.
     *
     * @return ShipmentDocumentInterface[]
     */
    public function getDocuments(): array;
}
