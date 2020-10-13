<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\OrderSelectionManager;

class RemoveUnusedConsumerServicesProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var OrderSelectionManager
     */
    private $selectionManager;

    /**
     * @var string[]
     */
    private $carrierConsumerServices;

    /**
     * RemoveUnusedConsumerServicesProcessor constructor.
     *
     * @param OrderSelectionManager $selectionManager
     * @param string[] $carrierConsumerServices
     */
    public function __construct(
        OrderSelectionManager $selectionManager,
        array $carrierConsumerServices = []
    ) {
        $this->selectionManager = $selectionManager;
        $this->carrierConsumerServices = $carrierConsumerServices;
    }

    /**
     * Remove services which were not chosen during checkout and cannot be booked during label creation.
     *
     * From the entirety of available shipping services, some are targeted towards consumers,
     * some are to be booked by the merchant. This processor removes all consumer services
     * from the shipping options that were not already selected during checkout. The merchant
     * should not be able to add any further consumer services on their behalf. The distinction
     * between merchant and consumer services is not available at the shipping option,
     * so the carriers need to register their consumer services via DI configuration.
     *
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
        if (!$shipment) {
            return $shippingOptions;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        $carrierCode = strtok((string) $order->getShippingMethod(), '_');
        $consumerServices = $this->carrierConsumerServices[$carrierCode] ?? [];

        if (empty($consumerServices)) {
            // no consumer services registered for the current carrier, nothing to do.
            return $shippingOptions;
        }

        // collect all codes of selected services
        $selectedServices = array_reduce(
            $this->selectionManager->load((int) $shipment->getShippingAddressId()),
            function (array $carry, SelectionInterface $selection) {
                $carry[$selection->getShippingOptionCode()] = $selection->getShippingOptionCode();
                return $carry;
            },
            []
        );

        $shippingOptions = array_filter(
            $shippingOptions,
            function (ShippingOptionInterface $shippingOption) use ($consumerServices, $selectedServices) {
                $code = $shippingOption->getCode();
                if (in_array($code, $consumerServices, true) && (!in_array($code, $selectedServices, true))) {
                    return false;
                }

                return true;
            }
        );

        return $shippingOptions;
    }
}
