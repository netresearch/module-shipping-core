<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Netresearch\ShippingCore\Model\Util\TemplateParser;

class CodReasonForPaymentProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var TemplateParser
     */
    private $templateParser;

    public function __construct(TemplateParser $templateParser)
    {
        $this->templateParser = $templateParser;
    }

    /**
     * Replace the CoD Reason for Payment template with actual values from the current order.
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
        if (!$shipment) {
            return $shippingOptions;
        }

        foreach ($shippingOptions as $shippingOption) {
            if ($shippingOption->getCode() !== Codes::SERVICE_OPTION_CASH_ON_DELIVERY) {
                // shipping option does not handle CoD, next…
                continue;
            }

            foreach ($shippingOption->getInputs() as $input) {
                if ($input->getCode() === Codes::SERVICE_INPUT_COD_REASON_FOR_PAYMENT) {
                    $value = $this->templateParser->parse($shipment->getOrder(), $input->getDefaultValue());
                    $input->setDefaultValue($value);
                }
            }
        }

        return $shippingOptions;
    }
}
