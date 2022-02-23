<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\Adminhtml\Config\Form;

use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Phrase;
use Netresearch\ShippingCore\Api\Util\AssetUrlInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\Config\Reader;

class AddCarrierLogosToGroup
{
    /**
     * @var AssetUrlInterface
     */
    private $assetRepo;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param AssetUrlInterface $assetRepo
     * @param Reader $reader
     */
    public function __construct(AssetUrlInterface $assetRepo, Reader $reader)
    {
        $this->assetRepo = $assetRepo;
        $this->reader = $reader;
    }

    public function afterGetData(Fieldset $subject, $data, $key = '')
    {
        if ($key !== 'legend') {
            return $data;
        }

        if (!in_array($subject->getId(), ['shipping_parcel_processing', 'shipping_batch_processing'], true)) {
            return $data;
        }

        $shippingSettings = $this->reader->read('adminhtml');
        $logos = array_filter(
            array_map(
                function (array $carrier) {
                    if (!isset($carrier['metadata'], $carrier['metadata']['logoUrl'])) {
                        return '';
                    }

                    return sprintf(
                        '<img alt="Carrier Logo" src="%s" />',
                        $this->assetRepo->get($carrier['metadata']['logoUrl'])
                    );
                },
                $shippingSettings['carriers']
            )
        );

        return $data . implode('', $logos);
    }
}
