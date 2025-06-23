<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap\InputValueInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMapInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\Config\ParcelProcessingConfig;
use Netresearch\ShippingCore\Model\ShippingBox\Package;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;

class ContainerInputDataProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var ParcelProcessingConfig
     */
    private $config;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var InputValueInterfaceFactory
     */
    private $inputValueFactory;

    /**
     * @var ValueMapInterfaceFactory
     */
    private $valueMapFactory;

    public function __construct(
        ParcelProcessingConfig $config,
        OptionInterfaceFactory $optionFactory,
        InputValueInterfaceFactory $inputValueFactory,
        ValueMapInterfaceFactory $valueMapFactory
    ) {
        $this->config = $config;
        $this->optionFactory = $optionFactory;
        $this->inputValueFactory = $inputValueFactory;
        $this->valueMapFactory = $valueMapFactory;
    }

    /**
     * Set options and value to "Container" input.
     *
     * @param InputInterface $input
     * @param Package[] $packages
     */
    private function setOptions(InputInterface $input, array $packages): void
    {
        $options = [];
        $defaultValue = '';

        foreach ($packages as $package) {
            $option = $this->optionFactory->create();
            $option->setValue($package->getId());
            $option->setLabel($package->getTitle());
            $options[] = $option;

            if ($package->isDefault()) {
                $defaultValue = $package->getId();
            }
        }

        $input->setOptions($options);
        $input->setDefaultValue($defaultValue);
    }

    /**
     * Set value maps to "Container" input.
     *
     * The merchant can pre-define a list of packages with dimensions and tare weight
     * properties. When a preset was chosen, the associated shipping options must be
     * updated accordingly. The association of one preset (source value) to the
     * corresponding shipping options (target codes and values) is expressed by a value map.
     *
     * ParentOption.inputValue1 => [
     *   [optionCode] => [inputValue],
     *   AssociatedOption1 => Foo,
     *   AssociatedOption2 => Bar,
     * ],
     * ParentOption.inputValue2 => [
     *   [optionCode] => [inputValue],
     *   AssociatedOption1 => Fox,
     *   AssociatedOption2 => Baz,
     * ]
     *
     * @param InputInterface $input
     * @param Package[] $packages
     */
    private function setValueMaps(InputInterface $input, array $packages): void
    {
        $maps = array_map(
            function (Package $package) {
                $width = $this->inputValueFactory->create();
                $width->setCode(Codes::PACKAGE_OPTION_DETAILS . '.' . Codes::PACKAGE_INPUT_WIDTH);
                $width->setValue((string) $package->getWidth());

                $length = $this->inputValueFactory->create();
                $length->setCode(Codes::PACKAGE_OPTION_DETAILS . '.' . Codes::PACKAGE_INPUT_LENGTH);
                $length->setValue((string) $package->getLength());

                $height = $this->inputValueFactory->create();
                $height->setCode(Codes::PACKAGE_OPTION_DETAILS . '.' . Codes::PACKAGE_INPUT_HEIGHT);
                $height->setValue((string) $package->getHeight());

                $weight = $this->inputValueFactory->create();
                $weight->setCode(Codes::PACKAGE_OPTION_DETAILS . '.' . Codes::PACKAGE_INPUT_PACKAGING_WEIGHT);
                $weight->setValue((string) $package->getWeight());

                $map = $this->valueMapFactory->create();
                $map->setSourceValue((string) $package->getId());
                $map->setInputValues([$width, $length, $height, $weight]);

                return $map;
            },
            $packages
        );

        $input->setValueMaps($maps);
    }

    /**
     * Specific input data processor for the "Container" input.
     *
     * Set the default package if configured and build the package properties value map.
     *
     * @param string $carrierCode
     * @param ShippingOptionInterface[] $shippingOptions
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return ShippingOptionInterface[]
     */
    #[\Override]
    public function process(
        string $carrierCode,
        array $shippingOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ?ShipmentInterface $shipment = null
    ): array {
        if (!isset($shippingOptions[Codes::PACKAGE_OPTION_DETAILS])) {
            // packageDetails shipping option does not exist, nothing to modify
            return $shippingOptions;
        }

        $shippingOption = $shippingOptions[Codes::PACKAGE_OPTION_DETAILS];
        $inputs = $shippingOption->getInputs();

        if (!isset($inputs[Codes::PACKAGE_INPUT_PACKAGING_ID])) {
            // customPackageId input does not exist, nothing to modify
            return $shippingOptions;
        }

        $packages = $this->config->getPackages($storeId);
        if (empty($packages)) {
            // no container presets configured, remove the input entirely
            unset($inputs[Codes::PACKAGE_INPUT_PACKAGING_ID]);
            $shippingOption->setInputs($inputs);
            return $shippingOptions;
        }

        $this->setOptions($inputs[Codes::PACKAGE_INPUT_PACKAGING_ID], $packages);
        $this->setValueMaps($inputs[Codes::PACKAGE_INPUT_PACKAGING_ID], $packages);

        return $shippingOptions;
    }
}
