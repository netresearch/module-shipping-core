<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Integration\TestCase\Model\ReturnShipment;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterfaceFactory;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Netresearch\ShippingCore\Model\ReturnShipment\Track;
use Netresearch\ShippingCore\Test\Integration\Fixture\OrderBuilder;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

class TrackRepositoryTest extends TestCase
{
    /**
     * @var Order
     */
    private static $order;

    /**
     * @throws \Exception
     */
    public static function createOrder(): void
    {
        self::$order = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate')->build();
    }

    /**
     * @throws \Exception
     */
    public static function createOrderRollback()
    {
        try {
            $orderFixture = new OrderFixture(self::$order);
            OrderFixtureRollback::create()->execute($orderFixture);
        } catch (\Exception $exception) {
            $argv = $_SERVER['argv'] ?? [];
            if (in_array('--verbose', $argv, true)) {
                $message = sprintf("Error during rollback: %s%s", $exception->getMessage(), PHP_EOL);
                register_shutdown_function('fwrite', STDERR, $message);
            }
        }
    }

    /**
     * @test
     * @magentoDataFixture createOrder
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveAndLoadTracks()
    {
        $trackFactory = Bootstrap::getObjectManager()->create(TrackInterfaceFactory::class);
        $documentFactory = Bootstrap::getObjectManager()->create(DocumentInterfaceFactory::class);
        $trackRepository = Bootstrap::getObjectManager()->create(TrackRepositoryInterface::class);

        $tracksData = [
            [
                TrackInterface::ORDER_ID => (int) self::$order->getEntityId(),
                TrackInterface::TITLE => 'Carrier Name and Method',
                TrackInterface::TRACK_NUMBER => '12345',
                TrackInterface::CARRIER_CODE => 'carrier_method',
                TrackInterface::DOCUMENTS => [
                    [
                        DocumentInterface::TITLE => 'PDF A',
                        DocumentInterface::LABEL_DATA => 'binary foo A',
                        DocumentInterface::MEDIA_TYPE => 'application/pdf',
                    ],
                    [
                        DocumentInterface::TITLE => 'Image A',
                        DocumentInterface::LABEL_DATA => 'binary bar A',
                        DocumentInterface::MEDIA_TYPE => 'image/png',
                    ],
                ],
            ],
            [
                TrackInterface::ORDER_ID => (int) self::$order->getEntityId(),
                TrackInterface::TITLE => 'Carrier Retoure',
                TrackInterface::TRACK_NUMBER => '67890',
                TrackInterface::CARRIER_CODE => 'carrier_method',
                TrackInterface::DOCUMENTS => [
                    [
                        DocumentInterface::TITLE => 'PDF B',
                        DocumentInterface::LABEL_DATA => 'binary foo B',
                        DocumentInterface::MEDIA_TYPE => 'application/pdf',
                    ],
                    [
                        DocumentInterface::TITLE => 'Image B',
                        DocumentInterface::LABEL_DATA => 'binary bar B',
                        DocumentInterface::MEDIA_TYPE => 'image/png',
                    ],
                ],
            ],
        ];

        foreach ($tracksData as $trackData) {
            $documentsData = $trackData[TrackInterface::DOCUMENTS];
            $trackData[TrackInterface::DOCUMENTS] = array_map(
                function (array $documentData) use ($documentFactory) {
                    return $documentFactory->create(['data' => $documentData]);
                },
                $documentsData
            );

            $track = $trackFactory->create(['data' => $trackData]);
            $trackRepository->save($track);

            self::assertNotEmpty($track->getEntityId());

            $loadedTrack = $trackRepository->get($track->getEntityId());
            self::assertSame($trackData[TrackInterface::ORDER_ID], $loadedTrack->getOrderId());
            self::assertSame($trackData[TrackInterface::TITLE], $loadedTrack->getTitle());
            self::assertSame($trackData[TrackInterface::TRACK_NUMBER], $loadedTrack->getTrackNumber());
            self::assertSame($trackData[TrackInterface::CARRIER_CODE], $loadedTrack->getCarrierCode());
            self::assertCount(count($documentsData), $loadedTrack->getDocuments());

            foreach ($documentsData as $documentData) {
                foreach ($loadedTrack->getDocuments() as $loadedDocument) {
                    if ($documentData[DocumentInterface::TITLE] === $loadedDocument->getTitle()) {
                        self::assertNotEmpty($loadedDocument->getEntityId());
                        self::assertSame($track->getEntityId(), $loadedDocument->getTrackId());
                        self::assertSame($documentData[DocumentInterface::MEDIA_TYPE], $loadedDocument->getMediaType());
                        self::assertSame($documentData[DocumentInterface::LABEL_DATA], $loadedDocument->getLabelData());
                    }
                }
            }
        }
    }
}
