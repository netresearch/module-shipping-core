<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\RequestModifier;

use Magento\Sales\Model\Order\Shipment;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestModifier\PackagingOptionReaderInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\PackagingDataProvider;

class PackagingOptionReader implements PackagingOptionReaderInterface
{
    /**
     * @var PackagingDataProvider
     */
    private $packagingDataProvider;

    /**
     * @var Shipment
     */
    private $shipment;

    /**
     * @var CarrierDataInterface
     */
    private $carrierData;

    public function __construct(PackagingDataProvider $packagingDataProvider, Shipment $shipment)
    {
        $this->packagingDataProvider = $packagingDataProvider;
        $this->shipment = $shipment;
    }

    /**
     * Initialize packaging data provider and extract carrier data.
     *
     * @return CarrierDataInterface
     * @throws \RuntimeException
     */
    private function getCarrierData(): CarrierDataInterface
    {
        if (!$this->carrierData) {
            if (!isset($this->shipment)) {
                throw new \RuntimeException('Cannot initialize packaging data, please provide shipment.');
            }

            $packagingData = $this->packagingDataProvider->getData($this->shipment);
            $carriers = $packagingData->getCarriers();
            if (empty($carriers)) {
                throw new \RuntimeException('Unable to load shipment request properties.');
            }

            $this->carrierData = current($carriers);
        }

        return $this->carrierData;
    }

    public function getPackageValues(): array
    {
        $packages = [];

        foreach ($this->getCarrierData()->getPackageOptions() as $optionCode => $packageOption) {
            foreach ($packageOption->getInputs() as $inputCode => $input) {
                $packages[$optionCode][$inputCode] = $input->getDefaultValue();
            }
        }

        return $packages;
    }

    public function getPackageOptionValues(string $optionCode): array
    {
        $packageValues = $this->getPackageValues();
        if (!isset($packageValues[$optionCode])) {
            throw new \RuntimeException("The package option '$optionCode' is not available.");
        }

        $packageOptionValues = [];

        foreach ($packageValues[$optionCode] as $inputCode => $value) {
            $packageOptionValues[$inputCode] = $value;
        }

        return $packageOptionValues;
    }

    public function getPackageOptionValue(string $optionCode, string $inputCode)
    {
        $packageOptionValues = $this->getPackageOptionValues($optionCode);

        if (!isset($packageOptionValues[$inputCode])) {
            throw new \RuntimeException(
                "The value '$inputCode' is not available for package option '$optionCode'."
            );
        }

        return $packageOptionValues[$inputCode];
    }

    public function getItemValues(int $orderItemId): array
    {
        $itemOptions = $this->getCarrierData()->getItemOptions();
        if (!isset($itemOptions[$orderItemId])) {
            throw new \RuntimeException("Options for order item '$orderItemId' are not available.");
        }

        $itemValues = [];

        foreach ($itemOptions[$orderItemId]->getShippingOptions() as $optionCode => $itemOption) {
            foreach ($itemOption->getInputs() as $inputCode => $input) {
                $itemValues[$optionCode][$inputCode] = $input->getDefaultValue();
            }
        }

        return $itemValues;
    }

    public function getItemOptionValues(int $orderItemId, string $optionCode): array
    {
        $itemValues = $this->getItemValues($orderItemId);
        if (!isset($itemValues[$optionCode])) {
            throw new \RuntimeException("The item option '$optionCode' is not available.");
        }

        $itemOptionValues = [];

        foreach ($itemValues[$optionCode] as $inputCode => $value) {
            $itemOptionValues[$inputCode] = $value;
        }

        return $itemOptionValues;
    }

    public function getItemOptionValue(int $orderItemId, string $optionCode, string $inputCode)
    {
        $itemOptionValues = $this->getItemOptionValues($orderItemId, $optionCode);
        if (!isset($itemOptionValues[$inputCode])) {
            throw new \RuntimeException(
                "The value '$inputCode' is not available for item option '$optionCode'."
            );
        }

        return $itemOptionValues[$inputCode];
    }

    public function getServiceValues(): array
    {
        $services = [];

        foreach ($this->getCarrierData()->getServiceOptions() as $serviceCode => $serviceOption) {
            foreach ($serviceOption->getInputs() as $inputCode => $input) {
                $services[$serviceCode][$inputCode] = $input->getDefaultValue();
            }
        }

        return $services;
    }

    public function getServiceOptionValues(string $serviceCode): array
    {
        $serviceValues = $this->getServiceValues();
        if (!isset($serviceValues[$serviceCode])) {
            throw new \RuntimeException("The service option '$serviceCode' is not available.");
        }

        $serviceOptionValues = [];

        foreach ($serviceValues[$serviceCode] as $inputCode => $value) {
            $serviceOptionValues[$inputCode] = $value;
        }

        return $serviceOptionValues;
    }

    public function getServiceOptionValue(string $serviceCode, string $inputCode)
    {
        $serviceOptionValues = $this->getServiceOptionValues($serviceCode);

        if (!isset($serviceOptionValues[$inputCode])) {
            throw new \RuntimeException(
                "The value '$inputCode' is not available for service option '$serviceCode'."
            );
        }

        return $serviceOptionValues[$inputCode];
    }
}
