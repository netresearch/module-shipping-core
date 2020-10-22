<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\OrderExport;

/**
 * A DTO with package parameter service rendering data for carriers that support it
 *
 * @api
 */
interface ServiceDataInterface
{
    public const CODE = 'code';
    public const DETAILS = 'details';

    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return \Netresearch\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface[]
     */
    public function getDetails(): array;
}
