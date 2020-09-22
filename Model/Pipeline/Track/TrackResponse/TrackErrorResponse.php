<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Track\TrackResponse;

use Magento\Framework\Phrase;
use Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackErrorResponseInterface;

class TrackErrorResponse extends TrackResponse implements TrackErrorResponseInterface
{
    /**
     * @return Phrase
     */
    public function getErrors(): Phrase
    {
        return $this->getData(self::ERRORS);
    }
}
