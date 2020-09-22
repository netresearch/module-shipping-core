<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse;

use Magento\Framework\Phrase;

/**
 * @api
 */
interface TrackErrorResponseInterface extends TrackResponseInterface
{
    public const ERRORS = 'errors';

    /**
     * @return Phrase
     */
    public function getErrors(): Phrase;
}
