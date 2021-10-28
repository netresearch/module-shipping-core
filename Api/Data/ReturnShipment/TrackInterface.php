<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ReturnShipment;

/**
 * @api
 */
interface TrackInterface
{
    public const ENTITY_ID = 'entity_id';
    public const ORDER_ID = 'order_id';
    public const CARRIER_CODE = 'carrier_code';
    public const TITLE = 'title';
    public const TRACK_NUMBER = 'track_number';
    public const CREATED_AT = 'created_at';

    public function getEntityId(): ?int;

    public function getOrderId(): int;

    public function getCarrierCode(): string;

    public function getTitle(): string;

    public function getTrackNumber(): string;

    public function getCreatedAt(): string;
}
