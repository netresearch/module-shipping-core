<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Config;

use Netresearch\ShippingCore\Api\Data\Config\ItemValidator\GroupInterface;
use Netresearch\ShippingCore\Api\Data\Config\ItemValidator\ResultInterface;
use Netresearch\ShippingCore\Api\Data\Config\ItemValidator\SectionInterface;

/**
 * A service that performs the validation of one config item or topic, e.g. sender address, API credentials, etc.
 *
 * @api
 */
interface ItemValidatorInterface extends SectionInterface, GroupInterface
{
    public function execute(int $storeId): ResultInterface;
}
