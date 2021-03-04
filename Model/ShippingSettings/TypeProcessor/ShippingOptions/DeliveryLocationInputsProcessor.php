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
    /**
     * @var InputInterfaceFactory
     */
    private $inputFactory;

    public function __construct(InputInterfaceFactory $inputFactory)
    {
        $this->inputFactory = $inputFactory;
    }

    /**
     * Generate delivery location service inputs.
     *
     * If the "deliveryLocation" service exists in the service options,
     * then additional hidden inputs will be generated to hold all the
     * relevant data that identify a location chosen on the map via
     * location finder input.
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
    public function process(
        string $carrierCode,
        array $shippingOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): array {
        foreach ($shippingOptions as $shippingOption) {
            if ($shippingOption->getCode() === Codes::SERVICE_OPTION_DELIVERY_LOCATION) {
                $additionalInputCodes = [
                    Codes::SERVICE_INPUT_DELIVERY_LOCATION_COMPANY,
                    Codes::SERVICE_INPUT_DELIVERY_LOCATION_TYPE,
                    Codes::SERVICE_INPUT_DELIVERY_LOCATION_NUMBER,
                    Codes::SERVICE_INPUT_DELIVERY_LOCATION_ID,
                    Codes::SERVICE_INPUT_DELIVERY_LOCATION_STREET,
                    Codes::SERVICE_INPUT_DELIVERY_LOCATION_POSTAL_CODE,
                    Codes::SERVICE_INPUT_DELIVERY_LOCATION_CITY,
                    Codes::SERVICE_INPUT_DELIVERY_LOCATION_COUNTRY_CODE,
                ];

                $inputs = $shippingOption->getInputs();
                foreach ($additionalInputCodes as $inputCode) {
                    $input = $this->inputFactory->create();
                    $input->setCode($inputCode);
                    $input->setInputType('hidden');
                    $inputs[$inputCode] = $input;
                }
                $shippingOption->setInputs($inputs);

                break;
            }
        }

        return $shippingOptions;
    }
}
