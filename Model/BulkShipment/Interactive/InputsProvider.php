<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\BulkShipment\Interactive;

use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\ShippingCore\Api\BulkShipment\Interactive\InputsProviderInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\OrderDataProviderInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\OrderDataProvider;

class InputsProvider implements InputsProviderInterface
{
    /**
     * @var OrderDataProviderInterface
     */
    private $orderDataProvider;

    /**
     * @var CarrierDataInterface[]
     */
    private $carrierData;

    /**
     * @param OrderDataProviderInterface $orderDataProvider
     */
    public function __construct(OrderDataProviderInterface $orderDataProvider)
    {
        $this->orderDataProvider = $orderDataProvider;
    }

    private function getCarrierData(OrderInterface $order): ?CarrierDataInterface
    {
        if (!isset($this->carrierData[$order->getEntityId()])) {
            /** @var \Magento\Sales\Model\Order $order */
            $carrierData = $this->orderDataProvider->getShippingOptions($order);
            $this->carrierData[$order->getEntityId()] = $carrierData;
        }

        return $this->carrierData[$order->getEntityId()];
    }

    public function getInput(OrderInterface $order, string $optionCode, string $inputCode): ?InputInterface
    {
        $carrierData = $this->getCarrierData($order);
        if (!$carrierData) {
            return null;
        }

        $shippingOptions = array_merge(
            $carrierData->getServiceOptions(),
            $carrierData->getPackageOptions()
        );

        foreach ($shippingOptions as $option) {
            if ($optionCode !== $option->getCode()) {
                continue;
            }
            foreach ($option->getInputs() as $input) {
                if ($input->getCode() === $inputCode) {
                    return $input;
                }
            }
        }

        return null;
    }
}
