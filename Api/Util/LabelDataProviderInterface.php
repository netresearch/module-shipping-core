<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Util;

use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;

/**
 * @api
 */
interface LabelDataProviderInterface
{
    /**
     * @param LabelResponseInterface $labelResponse
     * @return void
     */
    public function setLabelResponse(LabelResponseInterface $labelResponse);

    /**
     * @return LabelResponseInterface|null
     */
    public function getLabelResponse(): ?LabelResponseInterface;
}
