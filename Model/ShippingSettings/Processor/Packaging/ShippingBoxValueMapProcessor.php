<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Processor\Packaging;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap\InputValueInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\Config\ParcelProcessingConfig;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;

/**
 * Adds dynamic value mappings for the different available "My own package" presets to the "Container" input.
 */
class ShippingBoxValueMapProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var ParcelProcessingConfig
     */
    private $config;

    /**
     * @var ValueMapInterfaceFactory
     */
    private $valueMapFactory;

    /**
     * @var InputValueInterfaceFactory
     */
    private $inputValueFactory;

    public function __construct(
        ParcelProcessingConfig $config,
        ValueMapInterfaceFactory $valueMapFactory,
        InputValueInterfaceFactory $inputValueFactory
    ) {
        $this->config = $config;
        $this->valueMapFactory = $valueMapFactory;
        $this->inputValueFactory = $inputValueFactory;
    }

    /**
     * @param $scopeId
     *
     * @return ValueMapInterface[]
     */
    private function buildValueMaps($scopeId): array
    {
        $maps = [];

        foreach ($this->config->getPackages($scopeId) as $package) {
            $width = $this->inputValueFactory->create();
            $width->setCode(Codes::PACKAGING_OPTION_DETAILS . '.' . Codes::PACKAGING_INPUT_WIDTH);
            $width->setValue((string) $package->getWidth());

            $length = $this->inputValueFactory->create();
            $length->setCode(Codes::PACKAGING_OPTION_DETAILS . '.' . Codes::PACKAGING_INPUT_LENGTH);
            $length->setValue((string) $package->getLength());

            $height = $this->inputValueFactory->create();
            $height->setCode(Codes::PACKAGING_OPTION_DETAILS . '.' . Codes::PACKAGING_INPUT_HEIGHT);
            $height->setValue((string) $package->getHeight());

            $weight = $this->inputValueFactory->create();
            $weight->setCode(Codes::PACKAGING_OPTION_DETAILS . '.' . Codes::PACKAGING_INPUT_PACKAGING_WEIGHT);
            $weight->setValue((string) $package->getWeight());

            $map = $this->valueMapFactory->create();
            $map->setSourceValue((string) $package->getId());
            $map->setInputValues([$width, $length, $height, $weight]);
            $maps[] = $map;
        }

        return $maps;
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param ShipmentInterface $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, ShipmentInterface $shipment): array
    {
        if (
            !isset(
                $optionsData[Codes::PACKAGING_OPTION_DETAILS],
                $optionsData[Codes::PACKAGING_OPTION_DETAILS]
                ->getInputs()[Codes::PACKAGING_INPUT_CUSTOM_PACKAGE_ID]
            )
        ) {
            return $optionsData;
        }

        $input = $optionsData[Codes::PACKAGING_OPTION_DETAILS]
            ->getInputs()[Codes::PACKAGING_INPUT_CUSTOM_PACKAGE_ID];

        $input->setValueMaps($this->buildValueMaps($shipment->getStoreId()));

        return $optionsData;
    }
}
