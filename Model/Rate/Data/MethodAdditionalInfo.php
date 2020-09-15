<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Rate\Data;

use Netresearch\ShippingCore\Api\Data\Rate\MethodAdditionalInfoInterface;
use Magento\Framework\DataObject;

class MethodAdditionalInfo extends DataObject implements MethodAdditionalInfoInterface
{
    public function getDeliveryDate(): string
    {
        return (string) $this->getData(self::DELIVERY_DATE);
    }

    public function setDeliveryDate(string $deliveryDate): void
    {
        $this->setData(self::DELIVERY_DATE, $deliveryDate);
    }

    public function getCarrierLogoUrl(): string
    {
        return (string) $this->getData(self::CARRIER_LOGO_URL);
    }

    public function setCarrierLogoUrl(string $carrierLogoUrl): void
    {
        $this->setData(self::CARRIER_LOGO_URL, $carrierLogoUrl);
    }
}
