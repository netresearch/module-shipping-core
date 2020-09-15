<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Rate\Emulation;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Netresearch\ShippingCore\Api\Rate\ProxyCarrierConfigInterface;
use Netresearch\ShippingCore\Api\Rate\RateRequestEmulationInterface;

/**
 * Abstraction layer for providing the carrier with rates
 */
class RatesManagement
{
    /**
     * @var RateRequestEmulationInterface
     */
    private $rateRequestService;

    /**
     * @var ProxyCarrierConfigInterface
     */
    private $proxyConfig;

    /**
     * RatesManagement constructor.
     *
     * @param RateRequestEmulationInterface $rateRequestService
     * @param ProxyCarrierConfigInterface $proxyConfig
     */
    public function __construct(
        RateRequestEmulationInterface $rateRequestService,
        ProxyCarrierConfigInterface $proxyConfig
    ) {
        $this->rateRequestService = $rateRequestService;
        $this->proxyConfig = $proxyConfig;
    }

    /**
     * Fetch rates from emulated carrier.
     *
     * @param RateRequest $rateRequest
     * @return bool|Result
     */
    public function collectRates(RateRequest $rateRequest)
    {
        $storeId = $rateRequest->getStoreId();
        $carrierCode = $this->proxyConfig->getProxyCarrierCode($storeId);

        return $this->rateRequestService->emulateRateRequest($carrierCode, $rateRequest);
    }
}
