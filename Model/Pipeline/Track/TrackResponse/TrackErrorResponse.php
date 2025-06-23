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
     * @return string[]
     */
    #[\Override]
    public function getErrors(): array
    {
        $errors = $this->getData(self::ERRORS) ?? [];
        return array_map(static function ($error): string {
            return $error instanceof Phrase ? $error->render() : (string) $error;
        }, $errors);
    }
}
