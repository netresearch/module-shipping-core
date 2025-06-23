<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\ArrayProcessor;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\ArrayProcessor\ShippingSettingsProcessorInterface;
use Netresearch\ShippingCore\Model\Util\ShipmentItemFilter;

class PrepareItemOptionsProcessor implements ShippingSettingsProcessorInterface
{
    /**
     * @var ShipmentItemFilter
     */
    private $itemFilter;

    public function __construct(ShipmentItemFilter $itemFilter)
    {
        $this->itemFilter = $itemFilter;
    }

    /**
     * For each shippable item, create item options dynamically from the static options template.
     *
     * @param mixed[] $shippingSettings
     * @param int $storeId
     * @param ShipmentInterface|null $shipment
     *
     * @return mixed[]
     */
    #[\Override]
    public function process(array $shippingSettings, int $storeId, ?ShipmentInterface $shipment = null): array
    {
        if (!$shipment) {
            // no items available to apply the template for.
            return $shippingSettings;
        }

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());

        $itemOptions = [];

        // prepare item option list for every shippable item
        foreach ($items as $item) {
            $itemId = (int)$item->getOrderItemId();
            $itemOptions[$itemId] = [
                'itemId' => $itemId,
                'shippingOptions' => [],
            ];
        }

        foreach ($shippingSettings['carriers'] as $carrierCode => &$carrier) {
            if (isset($carrier['itemOptions']) && is_array($carrier['itemOptions'])) {
                foreach ($carrier['itemOptions'] as $itemShippingOptions) {
                    // copy the carrier's item shipping options to the prepared item
                    foreach ($itemOptions as &$itemOption) {
                        $itemOption['shippingOptions'] += $itemShippingOptions['shippingOptions'];
                    }
                }
            }

            $carrier['itemOptions'] = $itemOptions;
        }

        return $shippingSettings;
    }
}
