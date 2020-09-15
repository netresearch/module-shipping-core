<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Rate;

/**
 * @api
 */
interface MethodAdditionalInfoInterface
{
    public const ATTRIBUTE_KEY = 'additional_info';
    public const DELIVERY_DATE = 'delivery_date';
    public const CARRIER_LOGO_URL = 'carrier_logo_url';

    /**
     * @return string
     */
    public function getDeliveryDate(): string;

    /**
     * @param string $deliveryDate
     * @return void
     */
    public function setDeliveryDate(string $deliveryDate): void;

    /**
     * @return string
     */
    public function getCarrierLogoUrl(): string;

    /**
     * @param string $carrierLogoUrl
     * @return void
     */
    public function setCarrierLogoUrl(string $carrierLogoUrl): void;
}
