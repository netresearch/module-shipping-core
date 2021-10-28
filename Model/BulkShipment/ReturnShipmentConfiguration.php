<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\BulkShipment;

use Netresearch\ShippingCore\Api\BulkShipment\ReturnLabelCreationInterface;
use Netresearch\ShippingCore\Api\BulkShipment\ReturnShipmentConfigurationInterface;
use Netresearch\ShippingCore\Api\Pipeline\ReturnShipmentRequest\RequestModifierInterface;

class ReturnShipmentConfiguration
{
    /**
     * @var ReturnShipmentConfigurationInterface[]
     */
    private $configurations;

    /**
     * @param ReturnShipmentConfigurationInterface[] $configurations
     */
    public function __construct(array $configurations = [])
    {
        $this->configurations = $configurations;
    }

    /**
     * @param string $carrierCode
     * @return ReturnShipmentConfigurationInterface
     * @throws \RuntimeException
     */
    public function getCarrierConfiguration(string $carrierCode): ReturnShipmentConfigurationInterface
    {
        foreach ($this->configurations as $configuration) {
            if ($configuration->getCarrierCode() === $carrierCode) {
                return $configuration;
            }
        }
        throw new \RuntimeException("Return shipment configuration for carrier $carrierCode is not available.");
    }

    /**
     * @param string $carrierCode
     * @return RequestModifierInterface
     * @throws \RuntimeException
     */
    public function getRequestModifier(string $carrierCode): RequestModifierInterface
    {
        return $this->getCarrierConfiguration($carrierCode)->getRequestModifier();
    }

    /**
     * @param string $carrierCode
     * @return ReturnLabelCreationInterface
     * @throws \RuntimeException
     */
    public function getReturnShipmentService(string $carrierCode): ReturnLabelCreationInterface
    {
        $config = $this->getCarrierConfiguration($carrierCode);
        return $config->getLabelService();
    }

    /**
     * @return string[]
     */
    public function getCarrierCodes(): array
    {
        $carrierCodes = array_map(
            static function (ReturnShipmentConfigurationInterface $configuration) {
                return $configuration->getCarrierCode();
            },
            $this->configurations
        );

        return $carrierCodes;
    }
}
