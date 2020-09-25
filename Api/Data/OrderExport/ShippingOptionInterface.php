<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\OrderExport;

/**
 * @api
 */
interface ShippingOptionInterface
{
    const PACKAGE = 'package';
    const SERVICES = 'services';

    /**
     * @return \Netresearch\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface[]
     */
    public function getPackage(): array;

    /**
     * @return \Netresearch\ShippingCore\Api\Data\OrderExport\ServiceDataInterface[]
     */
    public function getServices(): array;
}
