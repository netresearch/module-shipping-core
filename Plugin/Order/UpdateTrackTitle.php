<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\Order;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Track;
use Netresearch\ShippingCore\Model\Shipping\GetProductName;

class UpdateTrackTitle
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GetProductName
     */
    private $getProductName;

    /**
     * A shipment's package definition as specified in packaging popup.
     *
     * This structure may hold multiple packages for one shipment.
     * We process the packages' params sequentially in the same order
     * as the tracks are added to the shipment.
     *
     * @var mixed[]
     */
    private $packages;

    public function __construct(RequestInterface $request, GetProductName $getProductName)
    {
        $this->request = $request;
        $this->getProductName = $getProductName;
        $this->packages = [];
    }

    /**
     * Update track title.
     *
     * By default, Magento sets the carrier title as track title and so does
     * the bulk shipment response processor. Replace the carrier title by
     * the selected shipping product name (identified by method code) instead.
     *
     * Note that The `shipping_product` package param is introduced by this
     * module, it does not exist in offline shipments or shipments created
     * through the Magento default packaging popup.
     *
     * @see \Magento\Shipping\Model\Shipping\LabelGenerator::addTrackingNumbersToShipment
     * @see \Netresearch\ShippingCore\Model\Pipeline\Shipment\ResponseProcessor\AddTrack::processResponse
     *
     * @param Shipment $shipment
     * @param Track $track
     * @return null
     */
    public function beforeAddTrack(Shipment $shipment, Track $track)
    {
        if ($this->request->getParam('title')) {
            // track title manually set via "Shipping and Tracking Information" form, do not change.
            return null;
        }

        if (empty($this->packages)) {
            $this->packages = $shipment->getPackages() ?? [];
        }

        $package = array_shift($this->packages);
        if (!is_array($package) || !isset($package['params'], $package['params']['shipping_product'])) {
            return null;
        }

        $productName = $this->getProductName->execute($track->getCarrierCode(), $package['params']['shipping_product']);
        if ($productName) {
            $track->setTitle($productName);
        }

        return null;
    }
}
