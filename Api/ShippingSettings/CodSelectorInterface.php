<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\ShippingSettings;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;

/**
 * Populate Cash on Delivery selection data.
 *
 * @api
 */
interface CodSelectorInterface
{
    /**
     * Add Cash on Delivery service data to the selection model.
     *
     * CoD is not a global feature. Carriers are responsible for the
     * shipping setting definition. Thus, only the carrier can set the
     * shipping option code and value.
     *
     * @param AssignedSelectionInterface $selection
     */
    public function assignCodSelection(AssignedSelectionInterface $selection);
}
