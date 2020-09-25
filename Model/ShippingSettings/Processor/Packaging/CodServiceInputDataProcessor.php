<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Processor\Packaging;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\Util\TemplateParser;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;

class CodServiceInputDataProcessor implements ShippingOptionsProcessorInterface
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
     * Replace the COD Reason for Payment template variables.
     *
     * @param ShippingOptionInterface $shippingOption
     * @param OrderInterface $order
     */
    private function processCashOnDeliveryInputs(ShippingOptionInterface $shippingOption, OrderInterface $order)
    {
        foreach ($shippingOption->getInputs() as $input) {
            if ($input->getCode() === Codes::SERVICE_INPUT_COD_REASON_FOR_PAYMENT) {
                $value = $this->templateParser->parse($order, $input->getDefaultValue());
                $input->setDefaultValue($value);
            }
        }
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param ShipmentInterface $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, ShipmentInterface $shipment): array
    {
        foreach ($optionsData as $optionGroup) {
            if ($optionGroup->getCode() === Codes::SERVICE_OPTION_CASH_ON_DELIVERY) {
                $this->processCashOnDeliveryInputs($optionGroup, $shipment->getOrder());
            }
        }

        return $optionsData;
    }
}
