<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse;

/**
 * A negative label response with reason phrase.
 *
 * @api
 */
interface ShipmentErrorResponseInterface extends ShipmentResponseInterface
{
    public const ERRORS = 'errors';

    /**
     * @return String[]
     */
    public function getErrors(): array;
}
