<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestExtractor;

/**
 * Read service data from the shipment request.
 */
interface ServiceOptionReaderInterface
{
    /**
     * Read a value from the service options, identified by option code and input code.
     *
     * @param string $optionCode
     * @param string $inputCode
     * @return mixed
     */
    public function getServiceOptionValue(string $optionCode, string $inputCode);

    /**
     * Check if a service, identified by option code,  was chosen.
     *
     * @param string $optionCode
     * @param string $inputCode
     * @return bool
     */
    public function isServiceEnabled(string $optionCode, string $inputCode = 'enabled'): bool;
}
