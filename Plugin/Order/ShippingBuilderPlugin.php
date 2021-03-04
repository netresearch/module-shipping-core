<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\Order;

use Magento\Sales\Api\Data\ShippingExtensionFactory;
use Magento\Sales\Api\Data\ShippingInterface;
use Magento\Sales\Api\Data\TotalExtensionInterfaceFactory;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\ShippingBuilder;
use Netresearch\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface;
use Netresearch\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\OrderExport\ServiceDataInterface;
use Netresearch\ShippingCore\Api\Data\OrderExport\ServiceDataInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\OrderExport\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\Data\OrderExport\ShippingOptionInterfaceFactory;
use Netresearch\ShippingCore\Model\AdditionalFee\TotalsManager;
use Netresearch\ShippingCore\Model\ShippingSettings\OrderDataProvider;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;

class ShippingBuilderPlugin
{
    /**
     * @var ShippingExtensionFactory
     */
    private $shippingExtensionFactory;

    /**
     * @var ServiceDataInterfaceFactory
     */
    private $serviceDataFactory;

    /**
     * @var ShippingOptionInterfaceFactory
     */
    private $packageDataFactory;

    /**
     * @var KeyValueObjectInterfaceFactory
     */
    private $keyValueObjectFactory;

    /**
     * @var OrderDataProvider
     */
    private $orderDataProvider;

    /**
     * @var TotalExtensionInterfaceFactory
     */
    private $totalExtensionFactory;

    public function __construct(
        ShippingExtensionFactory $shippingExtensionFactory,
        ServiceDataInterfaceFactory $serviceDataFactory,
        ShippingOptionInterfaceFactory $packageDataFactory,
        KeyValueObjectInterfaceFactory $keyValueObjectFactory,
        OrderDataProvider $orderDataProvider,
        TotalExtensionInterfaceFactory $totalExtensionFactory
    ) {
        $this->shippingExtensionFactory = $shippingExtensionFactory;
        $this->serviceDataFactory = $serviceDataFactory;
        $this->packageDataFactory = $packageDataFactory;
        $this->keyValueObjectFactory = $keyValueObjectFactory;
        $this->orderDataProvider = $orderDataProvider;
        $this->totalExtensionFactory = $totalExtensionFactory;
    }

    /**
     * For shipments, add the service information, custom product attributes, item data and
     * all customs data to the shipment.
     *
     * @param ShippingBuilder $shippingBuilder
     * @param ShippingInterface|null $shipping
     *
     * @return ShippingInterface
     * @see \Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\RequestModifier::modifyPackage
     *      for package/data structure
     */
    public function afterCreate(
        ShippingBuilder $shippingBuilder,
        ShippingInterface $shipping = null
    ): ShippingInterface {
        if (!$shipping) {
            return $shipping;
        }

        /** @var Address $orderAddress */
        $orderAddress = $shipping->getAddress();
        if (!$orderAddress) {
            return $shipping;
        }

        $extensionAttributes = $shipping->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->shippingExtensionFactory->create();
        }

        // create a temporary shipment for the order to be able to
        $order = $orderAddress->getOrder();

        $orderCarrierData = $this->orderDataProvider->getShippingOptions($order);
        if ($orderCarrierData === null) {
            return $shipping;
        }

        /** @var KeyValueObjectInterface[] $package */
        $package = [];
        /** @var ServiceDataInterface[] $services */
        $services = [];

        // Process general package options
        foreach ($orderCarrierData->getPackageOptions() as $shippingOption) {
            if ($shippingOption->getAvailable()) {
                foreach ($shippingOption->getInputs() as $input) {
                    // drop empty default values (meaning there was no preconfigured value)
                    if (empty($input->getDefaultValue())
                        || $input->getCode() === Codes::PACKAGE_INPUT_PACKAGING_ID
                    ) {
                        continue;
                    }

                    $package[] = $this->keyValueObjectFactory->create(
                        [
                            KeyValueObjectInterface::KEY => $input->getCode(),
                            KeyValueObjectInterface::VALUE => $input->getDefaultValue(),
                        ]
                    );
                }
            }
        }

        // Process service options
        foreach ($orderCarrierData->getServiceOptions() as $serviceOption) {
            if ($serviceOption->getAvailable()) {
                $serviceDetails = [];
                foreach ($serviceOption->getInputs() as $input) {
                    // filter services that are not enabled (default off)
                    if ($input->getCode() === 'enabled' && (int) $input->getDefaultValue() !== 1) {
                        continue;
                    }
                    $serviceDetails[] = $this->keyValueObjectFactory->create(
                        [
                            KeyValueObjectInterface::KEY => $input->getCode(),
                            KeyValueObjectInterface::VALUE => $input->getDefaultValue(),
                        ]
                    );
                }
                if (!empty($serviceDetails)) {
                    $services[] = $this->serviceDataFactory->create(
                        [
                            ServiceDataInterface::CODE => $serviceOption->getCode(),
                            ServiceDataInterface::DETAILS => $serviceDetails,
                        ]
                    );
                }
            }
        }

        $packageData = $this->packageDataFactory->create(
            [
                ShippingOptionInterface::PACKAGE => $package,
                ShippingOptionInterface::SERVICES => $services,
            ]
        );

        $extensionAttributes->setNrshippingShippingOptions($packageData);
        $shipping->setExtensionAttributes($extensionAttributes);

        if (!$shipping->getTotal()) {
            return $shipping;
        }

        $totalsExtensionAttributes = $shipping->getTotal()->getExtensionAttributes();
        if (!$totalsExtensionAttributes) {
            $totalsExtensionAttributes = $this->totalExtensionFactory->create();
        }

        $totalsExtensionAttributes->setBaseNrshippingAdditionalFee(
            $order->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME)
        );
        $totalsExtensionAttributes->setBaseNrshippingAdditionalFeeInclTax(
            $order->getData(TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME)
        );
        $totalsExtensionAttributes->setNrshippingAdditionalFee(
            $order->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
        );
        $totalsExtensionAttributes->setNrshippingAdditionalFeeInclTax(
            $order->getData(TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
        );

        $shipping->getTotal()->setExtensionAttributes($totalsExtensionAttributes);

        return $shipping;
    }
}
