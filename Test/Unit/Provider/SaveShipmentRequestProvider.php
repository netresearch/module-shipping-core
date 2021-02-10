<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Provider;

class SaveShipmentRequestProvider
{
    /**
     * @return mixed[]
     */
    public static function getRequestData(): array
    {
        return [
            'post_data' => file_get_contents(__DIR__ . '/_files/saveShipmentRequestData.json'),
            'packages' => [
                1 => [
                    'params' => [
                        'shipping_product' => 'V53WPAK',
                        'container' => '',
                        'weight' => '1.30',
                        'weight_units' => 'KILOGRAM',
                        'length' => '30',
                        'width' => '20',
                        'height' => '20',
                        'dimension_units' => 'CENTIMETER',
                        'content_type' => 'OTHER',
                        'content_type_other' => 'Commercial Goods',
                        'customs_value' => '64.00',
                        'customs' => [
                            'termsOfTrade' => 'DDU',
                            'customsFees' => '0',
                            'electronicExportNotification' => false,
                            'exportDescription' => 'Foo Bar',
                            'placeOfCommittal' => 'Leipzig',
                        ],
                        'foo' => 'bar',
                        'services' => [
                            'parcelAnnouncement' => [
                                'enabled' => false,
                            ],
                            'printOnlyIfCodeable' => [
                                'enabled' => true,
                            ],
                            'additionalInsurance' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'items' => [
                        159 => [
                            'qty' => 2,
                            'customs_value' => '32',
                            'price' => '32',
                            'name' => 'Voyage Yoga Bag',
                            'weight' => '0.55',
                            'product_id' => '8',
                            'order_item_id' => 159,
                            'sku' => '24-WB01',
                            'customs' => [
                                'hsCode' => '62104000',
                                'countryOfOrigin' => 'PL',
                                'exportDescription' => 'Foo Bar',
                            ],
                        ],
                    ],
                ],
                2 => [
                    'params' => [
                        'shipping_product' => 'V53WPAK',
                        'container' => '',
                        'weight' => '0.45',
                        'weight_units' => 'KILOGRAM',
                        'length' => '15',
                        'width' => '8',
                        'height' => '4',
                        'dimension_units' => 'CENTIMETER',
                        'content_type' => 'OTHER',
                        'content_type_other' => 'Commercial Goods',
                        'customs_value' => '60.00',
                        'customs' => [
                            'termsOfTrade' => 'DDU',
                            'customsFees' => '0',
                            'electronicExportNotification' => false,
                            'exportDescription' => 'Fox Baz',
                        ],
                        'foo' => 'fox',
                        'services' => [
                            'parcelAnnouncement' => [
                                'enabled' => false,
                            ],
                            'printOnlyIfCodeable' => [
                                'enabled' => true,
                            ],
                            'additionalInsurance' => [
                                'enabled' => false,
                            ],
                        ],
                    ],
                    'items' => [
                        157 => [
                            'qty' => 1,
                            'customs_value' => '60',
                            'price' => '60',
                            'name' => 'Typhon Performance Fleece-lined Jacket',
                            'weight' => '0.35',
                            'product_id' => '388',
                            'order_item_id' => 157,
                            'sku' => 'MJ11-M-Black',
                            'customs' => [
                                'hsCode' => '43039000',
                                'countryOfOrigin' => 'UA',
                                'exportDescription' => 'Fox Baz',
                            ],
                        ],
                    ],
                ],
            ]
        ];
    }
}
