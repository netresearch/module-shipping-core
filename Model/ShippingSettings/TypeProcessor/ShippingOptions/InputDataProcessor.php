<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Magento\Framework\Measure\Length;
use Magento\Framework\Measure\Weight;
use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\Config\ParcelProcessingConfig;
use Netresearch\ShippingCore\Model\Config\Source\ExportContentType;
use Netresearch\ShippingCore\Model\ItemAttribute\ShipmentItemAttributeReader;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;

class InputDataProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var ShippingConfigInterface
     */
    private $shippingConfig;

    /**
     * @var ParcelProcessingConfig
     */
    private $parcelConfig;

    /**
     * @var ShipmentItemAttributeReader
     */
    private $itemAttributeReader;

    /**
     * @var ExportContentType
     */
    private $contentTypeSource;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    public function __construct(
        ShippingConfigInterface $shippingConfig,
        ParcelProcessingConfig $parcelConfig,
        ShipmentItemAttributeReader $itemAttributeReader,
        ExportContentType $contentTypeSource,
        CommentInterfaceFactory $commentFactory,
        OptionInterfaceFactory $optionFactory
    ) {
        $this->shippingConfig = $shippingConfig;
        $this->parcelConfig = $parcelConfig;
        $this->itemAttributeReader = $itemAttributeReader;
        $this->contentTypeSource = $contentTypeSource;
        $this->commentFactory = $commentFactory;
        $this->optionFactory = $optionFactory;
    }

    /**
     * Set options and values to inputs on package level.
     *
     * @param ShippingOptionInterface $shippingOption
     * @param ShipmentInterface $shipment
     */
    private function processInputs(ShippingOptionInterface $shippingOption, ShipmentInterface $shipment): void
    {
        $defaultPackage = $this->parcelConfig->getDefaultPackage($shipment->getStoreId());

        foreach ($shippingOption->getInputs() as $input) {
            switch ($input->getCode()) {
                // shipping product
                case Codes::PACKAGE_INPUT_PRODUCT_CODE:
                    $option = $this->optionFactory->create();
                    $value = substr(strrchr((string) $shipment->getOrder()->getShippingMethod(), '_'), 1);
                    $option->setValue($value);
                    $option->setLabel(
                        $shipment->getOrder()->getShippingDescription()
                    );
                    $input->setOptions([$option]);
                    $input->setDefaultValue($value);
                    break;

                // weight
                case Codes::PACKAGE_INPUT_PACKAGING_WEIGHT:
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->shippingConfig->getWeightUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getWeight() : '');
                    break;

                case Codes::PACKAGE_INPUT_WEIGHT:
                    $itemTotalWeight = $this->itemAttributeReader->getTotalWeight($shipment);
                    $packagingWeight = $defaultPackage ? $defaultPackage->getWeight() : 0;
                    $totalWeight = $itemTotalWeight + $packagingWeight;
                    if (!empty($totalWeight)) {
                        $input->setDefaultValue((string) $totalWeight);
                    }

                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->shippingConfig->getWeightUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    break;

                case Codes::PACKAGE_INPUT_WEIGHT_UNIT:
                    $weightUnit = $this->shippingConfig->getWeightUnit($shipment->getStoreId()) === 'kg'
                        ? Weight::KILOGRAM
                        : Weight::POUND;
                    $input->setDefaultValue($weightUnit);
                    break;

                // dimensions
                case Codes::PACKAGE_INPUT_LENGTH:
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->shippingConfig->getDimensionUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getLength() : '');
                    break;

                case Codes::PACKAGE_INPUT_WIDTH:
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->shippingConfig->getDimensionUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getWidth() : '');
                    break;

                case Codes::PACKAGE_INPUT_HEIGHT:
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->shippingConfig->getDimensionUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getHeight() : '');
                    break;

                case Codes::PACKAGE_INPUT_SIZE_UNIT:
                    $dimensionsUnit = $this->shippingConfig->getDimensionUnit($shipment->getStoreId()) === 'cm'
                        ? Length::CENTIMETER
                        : Length::INCH;
                    $input->setDefaultValue($dimensionsUnit);
                    break;

                // customs
                case Codes::PACKAGE_INPUT_CUSTOMS_VALUE:
                    $price = $this->itemAttributeReader->getTotalPrice($shipment);
                    $currency = $shipment->getStore()->getBaseCurrency();
                    $currencySymbol = $currency->getCurrencySymbol() ?: $currency->getCode();
                    $comment = $this->commentFactory->create();
                    $comment->setContent($currencySymbol);
                    $input->setComment($comment);
                    $input->setDefaultValue((string) $price);
                    break;

                case Codes::PACKAGE_INPUT_CONTENT_TYPE:
                    $input->setOptions(
                        array_map(
                            function ($optionArray) {
                                $option = $this->optionFactory->create();
                                $option->setValue($optionArray['value']);
                                $option->setLabel((string)$optionArray['label']);
                                return $option;
                            },
                            $this->contentTypeSource->toOptionArray()
                        )
                    );
                    break;
            }
        }
    }

    /**
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
        if (!$shipment) {
            return $shippingOptions;
        }

        foreach ($shippingOptions as $shippingOption) {
            $this->processInputs($shippingOption, $shipment);
        }

        return $shippingOptions;
    }
}
