<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Processor\Packaging;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\Config\ParcelProcessingConfig;
use Netresearch\ShippingCore\Model\ShippingBox\Package;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;

/**
 * Package container input data processor
 *
 * This class is hooked into the shipping option data creation via di.xml.
 * It sets dynamic options for the "Container" input in the packaging popup
 */
class PackageContainerInputDataProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var ParcelProcessingConfig
     */
    private $config;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    public function __construct(
        ParcelProcessingConfig $config,
        OptionInterfaceFactory $optionFactory
    ) {
        $this->config = $config;
        $this->optionFactory = $optionFactory;
    }

    /**
     * Set options and default value for custom container input
     *
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
                $optionsData[Codes::PACKAGING_OPTION_DETAILS]->getInputs()[Codes::PACKAGING_INPUT_CUSTOM_PACKAGE_ID]
            )
        ) {
            return $optionsData;
        }

        $shippingOption = $optionsData[Codes::PACKAGING_OPTION_DETAILS];
        $packages = $this->config->getPackages($shipment->getStoreId());

        if (empty($packages)) {
            /**
             * If there are no custom containers configured, remove the input entirely
             */
            $inputs = $shippingOption->getInputs();
            unset($inputs[Codes::PACKAGING_INPUT_CUSTOM_PACKAGE_ID]);
            $shippingOption->setInputs($inputs);
            return $optionsData;
        }

        $containerInput = $shippingOption->getInputs()[Codes::PACKAGING_INPUT_CUSTOM_PACKAGE_ID];
        //fixme(nr): check expected format for input options, probably not Package models?
        $this->setInputOptions($containerInput, $packages);
        $this->setDefaultContainer($shipment, $containerInput);

        return $optionsData;
    }

    /**
     * Add options for custom containers
     *
     * @param InputInterface $containerInput
     * @param Package[] $customContainers
     */
    private function setInputOptions($containerInput, array $customContainers)
    {
        $containerInput->setOptions(
            array_map(
                function (Package $package) {
                    $option = $this->optionFactory->create();
                    $option->setValue($package->getId());
                    $option->setLabel($package->getTitle());

                    return $option;
                },
                $customContainers
            )
        );
    }

    /**
     * Set default container as default input value
     *
     * @param ShipmentInterface $shipment
     * @param InputInterface $containerInput
     */
    private function setDefaultContainer(ShipmentInterface $shipment, InputInterface $containerInput)
    {
        $defaultPackage = $this->config->getDefaultPackage($shipment->getStoreId());
        if ($defaultPackage) {
            $containerInput->setDefaultValue($defaultPackage->getId());
        }
    }
}
