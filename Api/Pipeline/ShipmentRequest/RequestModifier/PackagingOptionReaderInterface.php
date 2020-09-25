<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestModifier;

use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 */
interface PackagingOptionReaderInterface
{
    /**
     * Read all package options' settings.
     *
     * @return mixed[][]
     * @throws LocalizedException
     */
    public function getPackageValues(): array;

    /**
     * Read a package option's settings, identified by option code.
     *
     * @param string $optionCode
     * @return mixed[]
     * @throws LocalizedException
     */
    public function getPackageOptionValues(string $optionCode): array;

    /**
     * Read one package option setting, identified by option code and input code.
     *
     * @param string $optionCode
     * @param string $inputCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getPackageOptionValue(string $optionCode, string $inputCode);

    /**
     * Read an item's option settings, identified by item ID.
     *
     * @param int $orderItemId
     * @return mixed[][]
     * @throws LocalizedException
     */
    public function getItemValues(int $orderItemId): array;

    /**
     * Read an item option's settings, identified by item ID and option code.
     *
     * @param int $orderItemId
     * @param string $optionCode
     * @return mixed[]
     * @throws LocalizedException
     */
    public function getItemOptionValues(int $orderItemId, string $optionCode): array;

    /**
     * Read one item option setting, identified by item ID, option code and input code.
     *
     * @param int $orderItemId
     * @param string $optionCode
     * @param string $inputCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getItemOptionValue(int $orderItemId, string $optionCode, string $inputCode);

    /**
     * Read all service options' settings.
     *
     * @return string[][]
     * @throws LocalizedException
     */
    public function getServiceValues(): array;

    /**
     * Read a service option's settings, identified by service code.
     *
     * @param string $serviceCode
     * @return mixed[]
     * @throws LocalizedException
     */
    public function getServiceOptionValues(string $serviceCode): array;

    /**
     * Read one service option setting, identified by service code and input code.
     *
     * @param string $serviceCode
     * @param string $inputCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getServiceOptionValue(string $serviceCode, string $inputCode);
}
