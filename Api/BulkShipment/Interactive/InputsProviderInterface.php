<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\BulkShipment\Interactive;

use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;

/**
 * Service to provide a set of possible shipping option values for interactive mass action.
 *
 * @api
 */
interface InputsProviderInterface
{
    /**
     * @param OrderInterface $order
     * @param string $optionCode
     * @param string $inputCode
     * @return InputInterface|null
     */
    public function getInput(OrderInterface $order, string $optionCode, string $inputCode): ?InputInterface;
}
