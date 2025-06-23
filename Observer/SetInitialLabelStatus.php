<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netresearch\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;

class SetInitialLabelStatus implements ObserverInterface
{
    /**
     * @var LabelStatusManagementInterface
     */
    private $labelStatusManagement;

    public function __construct(LabelStatusManagementInterface $labelStatusManagement)
    {
        $this->labelStatusManagement = $labelStatusManagement;
    }

    /**
     * Trigger setting of initial label status.
     *
     * @param Observer $observer
     */
    #[\Override]
    public function execute(Observer $observer)
    {
        $order = $observer->getData('order');
        $this->labelStatusManagement->setInitialStatus($order);
    }
}
