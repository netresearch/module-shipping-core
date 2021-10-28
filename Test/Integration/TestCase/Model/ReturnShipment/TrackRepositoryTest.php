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
        $trackRepository = Bootstrap::getObjectManager()->create(TrackRepositoryInterface::class);
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);

        $trackDataA = [
            TrackInterface::ORDER_ID => self::$order->getEntityId(),
            TrackInterface::TITLE => 'Carrier Name and Method',
            TrackInterface::TRACK_NUMBER => '12345',
            TrackInterface::CARRIER_CODE => 'carrier_method',
        ];
        $trackDataB = [
            TrackInterface::ORDER_ID => self::$order->getEntityId(),
            TrackInterface::TITLE => 'Carrier Retoure',
            TrackInterface::TRACK_NUMBER => '67890',
            TrackInterface::CARRIER_CODE => 'carrier_method',
        ];
        $tracksData = [$trackDataA, $trackDataB];
        foreach ($tracksData as $trackData) {
            $track = $trackFactory->create(['data' => $trackData]);
            $trackRepository->save($track);
        }

        // assert that all tracks were persisted and loaded again
        $trackCollection = $trackRepository->getList($searchCriteriaBuilder->create());
        $tracks = $trackCollection->getItems();
        self::assertCount(count($tracksData), $tracks);

        foreach ($tracks as $track) {
            // some sanity checks for the getters
            self::assertNotEmpty($track->getEntityId());
            self::assertEquals(self::$order->getEntityId(), $track->getOrderId());
            self::assertNotEmpty($track->getCarrierCode());
            self::assertNotEmpty($track->getTitle());
            self::assertNotEmpty($track->getTrackNumber());
            self::assertNotEmpty($track->getCreatedAt());

            // find original track's data for the current track model
            $key = array_search($track->getTrackNumber(), array_column($tracksData, TrackInterface::TRACK_NUMBER));
            $originalData = $tracksData[$key];

            // read data from track model and compare with original data
            $data = $track->getData();
            self::assertEmpty(array_diff($originalData, $data));
        }

        // now load the tracks individually
        foreach ($tracks as $track) {
            if ($track->getTrackNumber() === $trackDataA[TrackInterface::TRACK_NUMBER]) {
                // via search criteria
                $searchCriteriaBuilder = $searchCriteriaBuilder->addFilter(
                    TrackInterface::TRACK_NUMBER,
                    $trackDataA[TrackInterface::TRACK_NUMBER]
                );
                $trackSearchResults = $trackRepository->getList($searchCriteriaBuilder->create())->getItems();
                self::assertCount(1, $trackSearchResults);
                $loadedTrack = array_shift($trackSearchResults);
                self::assertInstanceOf(Track::class, $loadedTrack);
                self::assertSame($track->getData(), $loadedTrack->getData());
            } elseif ($track->getTrackNumber() === $trackDataB[TrackInterface::TRACK_NUMBER]) {
                // via ID
                $loadedTrack = $trackRepository->get($track->getEntityId());
                self::assertInstanceOf(Track::class, $loadedTrack);
                self::assertSame($track->getData(), $loadedTrack->getData());
            }
        }
    }
}
