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
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentRepositoryInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\TrackRepositoryInterface;
use Netresearch\ShippingCore\Model\ReturnShipment\Document;
use Netresearch\ShippingCore\Model\ReturnShipment\Track;
use Netresearch\ShippingCore\Test\Integration\Fixture\OrderBuilder;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

class DocumentRepositoryTest extends TestCase
{
    /**
     * @var Order
     */
    private static $order;

    /**
     * @var Track
     */
    private static $track;

    /**
     * @throws \Exception
     */
    public static function createOrderAndTrack(): void
    {
        self::$order = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate')->build();

        $trackFactory = Bootstrap::getObjectManager()->create(TrackInterfaceFactory::class);
        $trackRepository = Bootstrap::getObjectManager()->create(TrackRepositoryInterface::class);
        $trackData = [
            TrackInterface::ORDER_ID => self::$order->getEntityId(),
            TrackInterface::TITLE => 'Carrier Name and Method',
            TrackInterface::TRACK_NUMBER => '12345',
            TrackInterface::CARRIER_CODE => 'carrier_method',
        ];
        self::$track = $trackFactory->create(['data' => $trackData]);

        $trackRepository->save(self::$track);
    }

    /**
     * @throws \Exception
     */
    public static function createOrderAndTrackRollback()
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
     * @magentoDataFixture createOrderAndTrack
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveAndLoadDocuments()
    {
        $documentFactory = Bootstrap::getObjectManager()->create(DocumentInterfaceFactory::class);
        $documentRepository = Bootstrap::getObjectManager()->create(DocumentRepositoryInterface::class);
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);

        $documentDataA = [
            DocumentInterface::TRACK_ID => self::$track->getEntityId(),
            DocumentInterface::TITLE => 'PDF Label',
            DocumentInterface::LABEL_DATA => 'binary foo',
            DocumentInterface::MIME_TYPE => 'application/pdf',
        ];
        $documentDataB = [
            DocumentInterface::TRACK_ID => self::$track->getEntityId(),
            DocumentInterface::TITLE => 'QR Code',
            DocumentInterface::LABEL_DATA => 'binary bar',
            DocumentInterface::MIME_TYPE => 'image/png',
        ];
        $documentsData = [$documentDataA, $documentDataB];
        foreach ($documentsData as $documentData) {
            $document = $documentFactory->create(['data' => $documentData]);
            $documentRepository->save($document);
        }

        // assert that all documents were persisted and loaded again
        $documentSearchResults = $documentRepository->getList($searchCriteriaBuilder->create());
        $documents = $documentSearchResults->getItems();
        self::assertCount(count($documentsData), $documents);

        foreach ($documents as $document) {
            // some sanity checks for the getters
            self::assertNotEmpty($document->getEntityId());
            self::assertSame(self::$track->getEntityId(), $document->getTrackId());
            self::assertNotEmpty($document->getTrackId());
            self::assertNotEmpty($document->getTitle());
            self::assertNotEmpty($document->getLabelData());
            self::assertNotEmpty($document->getMimeType());
            self::assertNotEmpty($document->getCreatedAt());

            // find original document's data for the current document model
            $key = array_search($document->getTitle(), array_column($documentsData, DocumentInterface::TITLE));
            $originalData = $documentsData[$key];

            // read data from document model and compare with original data
            $data = $document->getData();
            self::assertEmpty(array_diff($originalData, $data));
        }

        // now load the documents individually
        foreach ($documents as $document) {
            if ($document->getTitle() === $documentDataA[DocumentInterface::TITLE]) {
                // via search criteria
                $searchCriteriaBuilder = $searchCriteriaBuilder->addFilter(
                    DocumentInterface::TITLE,
                    $documentDataA[DocumentInterface::TITLE]
                );
                $documentSearchResult = $documentRepository->getList($searchCriteriaBuilder->create())->getItems();
                self::assertCount(1, $documentSearchResult);
                $loadedDocument = array_shift($documentSearchResult);
                self::assertInstanceOf(Document::class, $loadedDocument);
                self::assertSame($document->getData(), $loadedDocument->getData());
            } elseif ($document->getTitle() === $documentDataA[DocumentInterface::TITLE]) {
                // via ID
                $loadedDocument = $documentRepository->get($document->getEntityId());
                self::assertInstanceOf(Document::class, $loadedDocument);
                self::assertSame($document->getData(), $loadedDocument->getData());
            }
        }
    }
}
