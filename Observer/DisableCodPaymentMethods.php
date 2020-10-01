<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Model\Quote;
use Netresearch\ShippingCore\Api\PaymentMethod\MethodAvailabilityInterface;
use Netresearch\ShippingCore\Model\Config\ParcelProcessingConfig;

class DisableCodPaymentMethods implements ObserverInterface
{
    /**
     * @var ParcelProcessingConfig
     */
    private $config;

    /**
     * @var MethodAvailabilityInterface[]
     */
    private $codSupportMap;

    /**
     * DisableCodPaymentMethods constructor.
     *
     * @param ParcelProcessingConfig $config
     * @param MethodAvailabilityInterface[] $codSupportMap
     */
    public function __construct(
        ParcelProcessingConfig $config,
        array $codSupportMap = []
    ) {
        $this->config = $config;
        $this->codSupportMap = $codSupportMap;
    }

    /**
     * Disable cash on delivery payment methods if carrier does not support them for the given parameters.
     *
     * COD will not be disabled for virtual quotes, these will not be processed with a carrier.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var DataObject $checkResult */
        $checkResult = $observer->getData('result');
        /** @var Quote|null $quote */
        $quote = $observer->getData('quote');
        if ($quote === null || $checkResult->getData('is_available') === false || $quote->isVirtual()) {
            // not called in checkout or already unavailable
            return;
        }

        /** @var MethodInterface $methodInstance */
        $methodInstance = $observer->getData('method_instance');
        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        if (empty($shippingMethod)) {
            return;
        }

        $carrier = strtok($shippingMethod, '_');
        $isCodPaymentMethod = $this->config->isCodPaymentMethod($methodInstance->getCode(), $quote->getStoreId());

        if ($isCodPaymentMethod && isset($this->codSupportMap[$carrier])) {
            $checkResult->setData('is_available', $this->codSupportMap[$carrier]->isAvailable($quote));
        }
    }
}
