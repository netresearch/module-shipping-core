<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Model\PackagingPopup;

use Magento\Framework\Serialize\Serializer\Json;
use Netresearch\ShippingCore\Model\PackagingPopup\RequestData;
use Netresearch\ShippingCore\Model\PackagingPopup\RequestDataConverter;
use Netresearch\ShippingCore\Model\PackagingPopup\RequestDataFactory;
use Netresearch\ShippingCore\Test\Unit\Provider\SaveShipmentRequestProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RequestDataConverterTest extends TestCase
{
    public function dataProvider()
    {
        return [
            'xb_two_packages' => SaveShipmentRequestProvider::getRequestData(),
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider
     *
     * @param string $json nr packaging popup request data
     * @param mixed[] $packages core packaging popup request data
     */
    public function convert(string $json, array $packages)
    {
        /** @var RequestDataFactory|MockObject $requestDataFactory */
        $requestDataFactory = $this->createMock(RequestDataFactory::class);
        $requestDataFactory->method('create')->willReturnCallback(
            function (array $args) {
                return new RequestData(
                    $args['packages'],
                    $args['shipmentItems'],
                    $args['shipmentComment'],
                    $args['commentNotificationEnabled'],
                    $args['shipmentNotificationEnabled']
                );
            }
        );

        $converter = new RequestDataConverter(new Json(), $requestDataFactory);
        $requestData = $converter->getData($json);

        self::assertEquals($packages, $requestData->getPackages());
    }
}
