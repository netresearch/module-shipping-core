<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\CarrierDataProcessorInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\CompatibilityProcessorInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ItemShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\MetadataProcessorInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingDataProcessorInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;

class CompositeShippingDataProcessor implements ShippingDataProcessorInterface
{
    /**
     * @var CarrierDataProcessorInterface[]
     */
    private $carrierDataProcessors;

    /**
     * @var ShippingOptionsProcessorInterface[]
     */
    private $shippingOptionsProcessors;

    /**
     * @var ItemShippingOptionsProcessorInterface[]
     */
    private $itemShippingOptionsProcessors;

    /**
     * @var CompatibilityProcessorInterface[]
     */
    private $compatibilityProcessors;

    /**
     * @var MetadataProcessorInterface[]
     */
    private $metadataProcessors;

    /**
     * CompositeShippingDataProcessor constructor.
     *
     * @param CarrierDataProcessorInterface[] $carrierDataProcessors
     * @param ShippingOptionsProcessorInterface[] $shippingOptionsProcessors
     * @param ItemShippingOptionsProcessorInterface[] $itemShippingOptionsProcessors
     * @param CompatibilityProcessorInterface[] $compatibilityProcessors
     * @param MetadataProcessorInterface[] $metadataProcessors
     */
    public function __construct(
        array $carrierDataProcessors = [],
        array $shippingOptionsProcessors = [],
        array $itemShippingOptionsProcessors = [],
        array $compatibilityProcessors = [],
        array $metadataProcessors = []
    ) {
        $this->carrierDataProcessors = $carrierDataProcessors;
        $this->shippingOptionsProcessors = $shippingOptionsProcessors;
        $this->itemShippingOptionsProcessors = $itemShippingOptionsProcessors;
        $this->compatibilityProcessors = $compatibilityProcessors;
        $this->metadataProcessors = $metadataProcessors;
    }

    /**
     * @param ShippingDataInterface $shippingData
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return ShippingDataInterface
     */
    public function process(
        ShippingDataInterface $shippingData,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): ShippingDataInterface {
        $carriers = [];

        foreach ($shippingData->getCarriers() as $carrier) {
            // process shipping options
            $packageOptions = $carrier->getPackageOptions();
            $serviceOptions = $carrier->getServiceOptions();

            foreach ($this->shippingOptionsProcessors as $processor) {
                $packageOptions = $processor->process($packageOptions, $storeId, $countryCode, $postalCode, $shipment);
                $serviceOptions = $processor->process($serviceOptions, $storeId, $countryCode, $postalCode, $shipment);
            }

            $carrier->setPackageOptions($packageOptions);
            $carrier->setServiceOptions($serviceOptions);

            // process item shipping options
            $itemOptions = $carrier->getItemOptions();
            foreach ($this->itemShippingOptionsProcessors as $processor) {
                $itemOptions = $processor->process($itemOptions, $storeId, $countryCode, $postalCode, $shipment);
            }
            $carrier->setItemOptions($itemOptions);

            // process compatibility rules
            $rules = $carrier->getCompatibilityData();
            foreach ($this->compatibilityProcessors as $processor) {
                $rules = $processor->process($rules, $storeId, $countryCode, $postalCode, $shipment);
            }
            $carrier->setCompatibilityData($rules);

            // process metadata
            $metadata = $carrier->getMetadata();
            foreach ($this->metadataProcessors as $processor) {
                $processor->process($metadata, $storeId, $countryCode, $postalCode, $shipment);
            }
            $carrier->setMetadata($metadata);

            // process the whole carrier settings after the individual sections were processed
            foreach ($this->carrierDataProcessors as $processor) {
                $carrier = $processor->process($carrier, $storeId, $countryCode, $postalCode, $shipment);
            }

            $carriers[] = $carrier;
        }

        $shippingData->setCarriers($carriers);
        return $shippingData;
    }
}
