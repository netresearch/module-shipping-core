<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ReturnShipment;

/**
 * @api
 */
interface DocumentInterface
{
    public const ENTITY_ID = 'entity_id';
    public const TRACK_ID = 'track_id';
    public const TITLE = 'title';
    public const LABEL_DATA = 'label_data';
    public const MEDIA_TYPE = 'mime_type';
    public const CREATED_AT = 'created_at';

    public function getEntityId(): ?int;

    public function getTrackId(): int;

    public function getTitle(): string;

    public function getLabelData(): string;

    public function getMediaType(): string;

    public function getCreatedAt(): string;
}
