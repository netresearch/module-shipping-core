<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\Adminhtml\Shipping\Block\View;

use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Shipping\Block\Adminhtml\View;
use Netresearch\ShippingCore\Model\BulkShipment\BulkShipmentConfiguration;

class AddShipmentCancelButton
{
    /**
     * @var BulkShipmentConfiguration
     */
    private $bulkConfigProvider;

    public function __construct(BulkShipmentConfiguration $bulkConfigProvider)
    {
        $this->bulkConfigProvider = $bulkConfigProvider;
    }

    /**
     * Add a "Cancel Shipment" button to the shipment details page if the shipment has tracks from a supported carrier.
     *
     * @param View $viewBlock
     * @return null
     */
    public function beforeSetLayout(View $viewBlock)
    {
        $shipment = $viewBlock->getShipment();
        $carrierCode = strtok($shipment->getOrder()->getShippingMethod(), '_');

        try {
            $this->bulkConfigProvider->getBulkCancellationService($carrierCode);
        } catch (\RuntimeException $exception) {
            // cancellation not supported by given carrier
            return null;
        }

        $tracks = $viewBlock->getShipment()->getAllTracks();
        $carrierTracks = array_filter($tracks, static function (ShipmentTrackInterface $track) use ($carrierCode) {
            return ($track->getCarrierCode() === $carrierCode);
        });

        if (empty($carrierTracks)) {
            // no carrier tracks in shipment, nothing to cancel
            return null;
        }

        $shipmentId = $viewBlock->getShipment()->getId();
        $cancelUrl = $viewBlock->getUrl('nrshipping/shipment/cancel', ['shipment_id' => $shipmentId]);
        $viewBlock->addButton(
            'nrshipping_cancel_shipment',
            [
                'label' => __('Cancel Labels'),
                'onclick' => "setLocation('$cancelUrl')"
            ]
        );

        return null;
    }
}
