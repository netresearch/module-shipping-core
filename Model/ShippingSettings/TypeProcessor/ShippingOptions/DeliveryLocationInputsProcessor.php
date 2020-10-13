<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;

class DeliveryLocationInputsProcessor implements ShippingOptionsProcessorInterface
{
    /*
     * The virtual input's codes that will be added
     * to the shipping option with the shopfinder input.
     */
    private const INPUT_CODES = [
        Codes::SHOPFINDER_INPUT_COMPANY,
        Codes::SHOPFINDER_INPUT_LOCATION_TYPE,
        Codes::SHOPFINDER_INPUT_LOCATION_NUMBER,
        Codes::SHOPFINDER_INPUT_LOCATION_ID,
        Codes::SHOPFINDER_INPUT_STREET,
        Codes::SHOPFINDER_INPUT_POSTAL_CODE,
        Codes::SHOPFINDER_INPUT_CITY,
        Codes::SHOPFINDER_INPUT_COUNTRY_CODE,
    ];

    /**
     * @var InputInterfaceFactory
     */
    private $inputFactory;

    /**
     * DeliveryLocationInputsProcessor constructor.
     *
     * @param InputInterfaceFactory $inputFactory
     */
    public function __construct(InputInterfaceFactory $inputFactory)
    {
        $this->inputFactory = $inputFactory;
    }

    /**
     * @param ShippingOptionInterface[] $shippingOptions
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(
        array $shippingOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): array {
        $index = null;
        $shopFinder = null;
        foreach ($shippingOptions as $code => $shippingOption) {
            foreach ($shippingOption->getInputs() as $input) {
                if ($input->getInputType() === Codes::INPUT_TYPE_SHOPFINDER) {
                    $index = $code;
                    $shopFinder = $shippingOption;
                    break 2;
                }
            }
        }

        if ($shopFinder && $index) {
            $inputs = $shopFinder->getInputs();
            foreach (self::INPUT_CODES as $inputCode) {
                $input = $this->inputFactory->create();
                $input->setCode($inputCode);
                $input->setInputType('hidden');
                $inputs[$inputCode] = $input;
            }
            $shopFinder->setInputs($inputs);
            $shippingOptions[$index] = $shopFinder;
        }

        return $shippingOptions;
    }
}
