<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\Metadata;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\MetadataProcessorInterface;
use Netresearch\ShippingCore\Api\Util\AssetUrlInterface;

class ImageUrlProcessor implements MetadataProcessorInterface
{
    /**
     * @var AssetUrlInterface
     */
    private $assetUrl;

    public function __construct(AssetUrlInterface $assetUrl)
    {
        $this->assetUrl = $assetUrl;
    }

    /**
     * Convert the image ID to its actual image URL in the current theme context.
     *
     * Note that the asset repository has a bug which prevents calculating the correct image url
     * when called from a different area than frontend or adminhtml (e.g. webapi_rest).
     * Area emulation does not help either as the theme does not get properly initialized.
     * The workaround is to load the configured frontend theme manually.
     *
     * @param string $carrierCode
     * @param MetadataInterface $metadata
     * @param int|null $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return MetadataInterface
     */
    public function process(
        string $carrierCode,
        MetadataInterface $metadata,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): MetadataInterface {
        $imageId = $metadata->getLogoUrl();
        if (!$imageId) {
            return $metadata;
        }

        $metadata->setLogoUrl($this->assetUrl->get($imageId));
        return $metadata;
    }
}
