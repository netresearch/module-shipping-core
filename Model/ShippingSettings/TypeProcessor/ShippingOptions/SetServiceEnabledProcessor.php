<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ShippingOptions;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\OrderSelectionManager;

class SetServiceEnabledProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var OrderSelectionManager
     */
    private $selectionManager;

    /**
     * SetServiceEnabledProcessor constructor.
     *
     * @param OrderSelectionManager $selectionManager
     */
    public function __construct(OrderSelectionManager $selectionManager)
    {
        $this->selectionManager = $selectionManager;
    }

    /**
     * Some services are enabled implicitly, e.g. by setting their "details"
     * property. For display in packaging popup, update the "enabled" setting
     * if added via adminhtml/shipping_settings.xml file.
     *
     * @param ShippingOptionInterface[] $shippingOptions
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(
        array $shippingOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): array {
        if (!$shipment) {
            return $shippingOptions;
        }

        // re-build service array, index by service option code
        $serviceSelection = array_reduce(
            $this->selectionManager->load((int) $shipment->getShippingAddressId()),
            function (array $carry, SelectionInterface $selection) {
                $carry[$selection->getShippingOptionCode()][$selection->getInputCode()] = $selection->getInputValue();
                return $carry;
            },
            []
        );

        foreach ($shippingOptions as $shippingOption) {
            if (!isset($serviceSelection[$shippingOption->getCode()])) {
                // no selection made for current shipping option, next…
                continue;
            }

            foreach ($shippingOption->getInputs() as $input) {
                if ($input->getCode() === 'enabled') {
                    $input->setDefaultValue('1');
                }
            }
        }

        return $shippingOptions;
    }
}
