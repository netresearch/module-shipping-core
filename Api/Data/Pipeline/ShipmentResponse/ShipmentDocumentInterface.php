<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse;

/**
 * An individual document of a positive (multi-)label response.
 *
 * @api
 */
interface ShipmentDocumentInterface
{
    public const TITLE = 'title';
    public const LABEL_DATA = 'label_data';
    public const MIME_TYPE = 'mime_type';

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getLabelData(): string;

    /**
     * @return string
     */
    public function getMimeType(): string;
}
