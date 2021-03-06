<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Pipeline;

use Magento\Shipping\Model\Shipment\Request;
use Netresearch\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;

/**
 * Create shipping labels at a web service by passing Magento shipment requests through multiple configurable stages.
 *
 * When a shipping label is to be created for a placed order, Magento provides all relevant shipping data to the carrier
 * as a "shipment request" object. Pipeline stages process and transform the request to become suitable for sending
 * it to the carrier-specific web service, then send and transform it back to application data. The result of running
 * the pipeline is the artifacts container where all stages add their output data to. Consequently, the overall pipeline
 * result (i.e. the label data and tracking number) can afterwards be obtained from the artifacts container.
 *
 * @see ArtifactsContainerInterface
 * @see CreateShipmentsStageInterface
 * @see ShipmentResponseProcessorInterface
 *
 * @api
 */
interface CreateShipmentsPipelineInterface
{
    /**
     * Initialize pipeline and execute configured stages.
     *
     * @param int $storeId
     * @param Request[] $requests
     * @return ArtifactsContainerInterface
     */
    public function run(int $storeId, array $requests): ArtifactsContainerInterface;
}
