<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings;

use Magento\Framework\Config\ReaderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\Processor\Packaging\ArrayProcessor\PackagingArrayCompositeProcessor;
use Netresearch\ShippingCore\Model\ShippingSettings\Processor\Packaging\PackagingDataCompositeProcessor;

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
     * @var PackagingArrayCompositeProcessor
     */
    private $compositeArrayProcessor;

    /**
     * @var PackagingDataCompositeProcessor
     */
    private $compositeDataProcessor;

    /**
     * @var ShippingDataHydrator
     */
    private $shippingDataHydrator;

    /**
     * @var ShippingDataInterface[]
     */
    private $shipmentData = [];

    public function __construct(
        ReaderInterface $reader,
        PackagingArrayCompositeProcessor $compositeArrayProcessor,
        PackagingDataCompositeProcessor $compositeDataProcessor,
        ShippingDataHydrator $shippingDataHydrator
    ) {
        $this->reader = $reader;
        $this->compositeArrayProcessor = $compositeArrayProcessor;
        $this->compositeDataProcessor = $compositeDataProcessor;
        $this->shippingDataHydrator = $shippingDataHydrator;
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

        $packagingDataArray = $this->reader->read('adminhtml');
        $packagingDataArray = $this->compositeArrayProcessor->process($packagingDataArray, $shipment);

        $packagingData = $this->shippingDataHydrator->toObject($packagingDataArray);
        $packagingData = $this->compositeDataProcessor->process($packagingData, $shipment);

        if (!empty($shipment->getEntityId())) {
            // cache packaging data if shipment has an identifier
            $this->shipmentData[$shipment->getEntityId()] = $packagingData;
        }

        return $packagingData;
    }
}
