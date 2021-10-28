<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ReturnShipment;

/**
 * Provide access to the label binary.
 *
 * @api
 */
interface DocumentLinkInterface
{
    public const TITLE = 'title';
    public const URL = 'url';

    public function getTitle(): string;

    public function getUrl(): string;
}
