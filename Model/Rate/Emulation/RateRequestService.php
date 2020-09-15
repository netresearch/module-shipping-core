<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Rate\Emulation;

use Magento\Framework\Exception\NotFoundException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Netresearch\ShippingCore\Api\Pipeline\RateResponseProcessorInterface;
use Netresearch\ShippingCore\Api\Rate\ProxyCarrierFactoryInterface;
use Netresearch\ShippingCore\Api\Rate\RateRequestEmulationInterface;
use Psr\Log\LoggerInterface;

class RateRequestService implements RateRequestEmulationInterface
{
    /**
     * @var ProxyCarrierFactoryInterface
     */
    private $proxyCarrierFactory;

    /**
     * @var RateResponseProcessorInterface
     */
    private $responseProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AbstractCarrierInterface[]
     */
    private $emulatedCarriers = [];

    /**
     * RateRequestEmulator constructor.
     *
     * @param ProxyCarrierFactory $proxyCarrierFactory
     * @param RateResponseProcessorInterface $responseProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProxyCarrierFactory $proxyCarrierFactory,
        RateResponseProcessorInterface $responseProcessor,
        LoggerInterface $logger
    ) {
        $this->proxyCarrierFactory = $proxyCarrierFactory;
        $this->responseProcessor = $responseProcessor;
        $this->logger = $logger;
    }

    /**
     * Retrieve rates from a given proxy carrier and process the result.
     *
     * @param string $carrierCode Carrier code to emulate
     * @param RateRequest $request Original rate request
     * @return Result|bool
     */
    public function emulateRateRequest(string $carrierCode, RateRequest $request)
    {
        if (!array_key_exists($carrierCode, $this->emulatedCarriers)) {
            try {
                $proxyCarrier = $this->proxyCarrierFactory->create($carrierCode);
                $this->emulatedCarriers[$carrierCode] = $proxyCarrier;
            } catch (NotFoundException $exception) {
                $this->logger->error($exception->getLogMessage());

                return false;
            } catch (\Exception $exception) {
                $logMessage = sprintf('Carrier "%s" could not be created: %s', $carrierCode, $exception->getMessage());
                $this->logger->error($logMessage);

                return false;
            }
        }

        $rateResult = $this->emulatedCarriers[$carrierCode]->collectRates($request);
        if ($rateResult instanceof Result) {
            $this->responseProcessor->processMethods($rateResult->getAllRates(), $request);
        }

        return $rateResult;
    }
}
