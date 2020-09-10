<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\InfoBox;

/**
 * Module version provider.
 *
 * @api
 */
interface VersionInterface
{
    /**
     * Obtain the module version for display in the config info box.
     *
     * @return string
     */
    public function getModuleVersion(): string;
}
