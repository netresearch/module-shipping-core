<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Netresearch\ShippingCore\Model\AdditionalFee\TotalsManager;

class AddAdditionalFeeToOrder implements ObserverInterface
{
    /**
     * @var TotalsManager
     */
    private $totalsManager;

    public function __construct(TotalsManager $totalsManager)
    {
        $this->totalsManager = $totalsManager;
    }

    /**
     * Transfer additional fees from Quote to Order on order creation
     *
     * @event sales_model_service_quote_submit_before
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getData('quote');
        /** @var Order $order */
        $order = $observer->getData('order');

        $this->totalsManager->transferAdditionalFees($quote, $order);
    }
}
