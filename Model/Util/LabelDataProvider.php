<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Netresearch\ShippingCore\Api\Util\LabelDataProviderInterface;

/**
 * Registry for passing a web service response through the application.
 */
class LabelDataProvider implements LabelDataProviderInterface
{
    /**
     * @var LabelResponseInterface
     */
    private $labelResponse;

    public function setLabelResponse(LabelResponseInterface $labelResponse)
    {
        $this->labelResponse = $labelResponse;
    }

    public function getLabelResponse(): ?LabelResponseInterface
    {
        return $this->labelResponse;
    }
}
