<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings;

use Magento\Framework\Config\ReaderInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\ArrayProcessor\ShippingSettingsProcessorInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingDataProcessorInterface;

class PackagingDataProvider
{
    public const GROUP_PACKAGE = 'packageOptions';
    public const GROUP_ITEM = 'itemOptions';
    public const GROUP_SERVICE = 'serviceOptions';

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var ShippingDataHydrator
     */
    private $shippingDataHydrator;

    /**
     * @var ShippingSettingsProcessorInterface
     */
    private $shippingSettingsProcessor;

    /**
     * @var ShippingDataProcessorInterface
     */
    private $shippingDataProcessor;

    /**
     * @var ShippingDataInterface[]
     */
    private $shipmentData = [];

    public function __construct(
        ReaderInterface $reader,
        ShippingDataHydrator $shippingDataHydrator,
        ShippingSettingsProcessorInterface $shippingSettingsProcessor,
        ShippingDataProcessorInterface $shippingDataProcessor
    ) {
        $this->reader = $reader;
        $this->shippingDataHydrator = $shippingDataHydrator;
        $this->shippingSettingsProcessor = $shippingSettingsProcessor;
        $this->shippingDataProcessor = $shippingDataProcessor;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return ShippingDataInterface
     * @throws \RuntimeException
     */
    public function getData(ShipmentInterface $shipment): ShippingDataInterface
    {
        if (!empty($shipment->getEntityId()) && isset($this->shipmentData[$shipment->getEntityId()])) {
            // use cached packaging data
            return $this->shipmentData[$shipment->getEntityId()];
        }

        $shippingSettings = $this->reader->read('adminhtml');
        $shippingSettings = $this->shippingSettingsProcessor->process(
            $shippingSettings,
            (int) $shipment->getStoreId(),
            $shipment
        );

        /** @var OrderAddressInterface $shippingAddress */
        $shippingAddress = $shipment->getShippingAddress();

        $shippingData = $this->shippingDataHydrator->toObject($shippingSettings);
        $shippingData = $this->shippingDataProcessor->process(
            $shippingData,
            (int) $shipment->getStoreId(),
            $shippingAddress->getCountryId(),
            (string) $shippingAddress->getPostcode(),
            $shipment
        );

        if (!empty($shipment->getEntityId())) {
            // cache packaging data if shipment has an identifier
            $this->shipmentData[$shipment->getEntityId()] = $shippingData;
        }

        return $shippingData;
    }
}
