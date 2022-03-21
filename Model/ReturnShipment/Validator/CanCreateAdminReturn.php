<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment\Validator;

use Magento\Framework\Module\ModuleList;
use Magento\Sales\Api\Data\OrderInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\CanCreateReturnInterface;
use Netresearch\ShippingCore\Model\ReturnShipment\Validator\CanCreateReturn;

class CanCreateAdminReturn implements CanCreateReturnInterface
{
    /**
     * @var ModuleList
     */
    private $moduleList;

    /**
     * @var CanCreateReturn
     */
    private $canCreateReturn;

    public function __construct(ModuleList $moduleList, CanCreateReturn $canCreateReturn)
    {
        $this->moduleList = $moduleList;
        $this->canCreateReturn = $canCreateReturn;
    }

    /**
     * Check if a return shipment can be fulfilled for the given order by any or the specified carrier.
     *
     * In admin panel, the feature is not enabled if the core RMA module is installed.
     *
     * @param OrderInterface $order
     * @param string|null $carrierCode
     * @return bool
     */
    public function execute(OrderInterface $order, string $carrierCode = null): bool
    {
        return !$this->moduleList->has('Magento_Rma') && $this->canCreateReturn->execute($order, $carrierCode);
    }
}
