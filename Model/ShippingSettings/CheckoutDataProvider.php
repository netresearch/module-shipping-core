<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings;

use Magento\Framework\Config\ReaderInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\ArrayProcessor\ShippingSettingsProcessorInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingDataProcessorInterface;

class CheckoutDataProvider
{
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
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     *
     * @return ShippingDataInterface
     *
     * @throws \RuntimeException
     */
    public function getData(int $storeId, string $countryCode, string $postalCode): ShippingDataInterface
    {
        $shippingSettings = $this->reader->read('frontend');
        $shippingSettings = $this->shippingSettingsProcessor->process($shippingSettings, $storeId);

        $shippingData = $this->shippingDataHydrator->toObject($shippingSettings);
        $shippingData = $this->shippingDataProcessor->process($shippingData, $storeId, $countryCode, $postalCode);

        return $shippingData;
    }
}
