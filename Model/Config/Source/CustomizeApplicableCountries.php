<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\UrlInterface;

/**
 * Config source model for the `sallowspecific` setting.
 */
class CustomizeApplicableCountries implements OptionSourceInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    #[\Override]
    public function toOptionArray(): array
    {
        $configUrl = $this->urlBuilder->getUrl('adminhtml/system_config/edit', [
            'section' => 'general',
            '_fragment' => 'general_country-link',
        ]);

        return [
            [
                'value' => '0',
                'label' => __('Use countries from <a href="%1">Allowed Countries</a> setting.', $configUrl)
            ],
            [
                'value' => '1',
                'label' => __('Create a customized country list')
            ],
        ];
    }
}
