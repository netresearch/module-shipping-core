<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings;

use Magento\Framework\Config\ReaderInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\Processor\Checkout\ArrayProcessor\CheckoutArrayCompositeProcessor;
use Netresearch\ShippingCore\Model\ShippingSettings\Processor\Checkout\CheckoutDataCompositeProcessor;

class CheckoutDataProvider
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var CheckoutArrayCompositeProcessor
     */
    private $compositeArrayProcessor;

    /**
     * @var CheckoutDataCompositeProcessor
     */
    private $compositeDataProcessor;

    /**
     * @var ShippingDataHydrator
     */
    private $shippingDataHydrator;

    public function __construct(
        ReaderInterface $reader,
        CheckoutArrayCompositeProcessor $compositeArrayProcessor,
        CheckoutDataCompositeProcessor $compositeDataProcessor,
        ShippingDataHydrator $shippingDataHydrator
    ) {
        $this->reader = $reader;
        $this->compositeArrayProcessor = $compositeArrayProcessor;
        $this->compositeDataProcessor = $compositeDataProcessor;
        $this->shippingDataHydrator = $shippingDataHydrator;
    }

    /**
     * @param string $countryCode
     * @param int $storeId
     * @param string $postalCode
     *
     * @return ShippingDataInterface
     *
     * @throws \RuntimeException
     */
    public function getData(string $countryCode, int $storeId, string $postalCode): ShippingDataInterface
    {
        $shippingDataArray = $this->reader->read('frontend');

        $shippingDataArray = $this->compositeArrayProcessor->process(
            $shippingDataArray,
            $storeId
        );

        $shippingData = $this->shippingDataHydrator->toObject($shippingDataArray);

        return $this->compositeDataProcessor->process(
            $shippingData,
            $countryCode,
            $postalCode,
            $storeId
        );
    }
}
