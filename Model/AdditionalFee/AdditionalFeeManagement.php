<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\AdditionalFee;

use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote;
use Netresearch\ShippingCore\Api\AdditionalFee\AdditionalFeeConfigurationInterface;

class AdditionalFeeManagement
{
    /**
     * @var AdditionalFeeConfigurationInterface[]
     */
    private $configurations;

    /**
     * AdditionalFeeManagement constructor.
     *
     * @param AdditionalFeeConfigurationInterface[] $configurations
     */
    public function __construct(array $configurations = [])
    {
        $this->configurations = $configurations;
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    public function isActive(Quote $quote): bool
    {
        $carrierCode = strtok((string) $quote->getShippingAddress()->getShippingMethod(), '_');
        if (!$carrierCode) {
            return false;
        }

        try {
            $configuration = $this->getConfigurationForCarrierCode($carrierCode);

            return $configuration->isActive($quote);
        } catch (\RuntimeException $e) {
            return false;
        }
    }

    /**
     * @param Quote $quote
     * @return float
     */
    public function getTotalAmount(Quote $quote): float
    {
        $carrierCode = strtok((string) $quote->getShippingAddress()->getShippingMethod(), '_');
        if (!$carrierCode) {
            return 0.0;
        }

        try {
            $configuration = $this->getConfigurationForCarrierCode($carrierCode);

            return $configuration->getServiceCharge($quote);
        } catch (\RuntimeException $e) {
            return 0.0;
        }
    }

    public function getLabel(string $carrierCode): Phrase
    {
        try {
            $configuration = $this->getConfigurationForCarrierCode($carrierCode);

            return $configuration->getLabel();
        } catch (\RuntimeException $e) {
            return __('Additional Fee');
        }
    }

    /**
     * @param string $carrierCode
     * @return AdditionalFeeConfigurationInterface
     * @throws \RuntimeException
     */
    private function getConfigurationForCarrierCode(string $carrierCode): AdditionalFeeConfigurationInterface
    {
        foreach ($this->configurations as $configuration) {
            if ($configuration->getCarrierCode() === $carrierCode) {
                return $configuration;
            }
        }

        throw new \RuntimeException('No configuration found for given carrier code.');
    }
}
