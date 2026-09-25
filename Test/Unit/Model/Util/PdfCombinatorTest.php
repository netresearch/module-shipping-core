<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Unit\Model\Util;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Filesystem;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Shipping\Model\CarrierFactory;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Shipping\Model\Shipping\LabelsFactory;
use Netresearch\ShippingCore\Model\Util\PdfCombinator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The Parcel DE Shipping API delivers PDF 2.0 documents, which Zend_Pdf refuses to parse.
 */
class PdfCombinatorTest extends TestCase
{
    private const FIXTURE_DIR = __DIR__ . '/../../../_files/pdf/';

    /**
     * @var PdfCombinator
     */
    private PdfCombinator $pdfCombinator;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $labelGenerator = new LabelGenerator(
            $this->createStub(CarrierFactory::class),
            $this->createStub(LabelsFactory::class),
            $this->createStub(ScopeConfigInterface::class),
            $this->createStub(TrackFactory::class),
            $this->createStub(Filesystem::class)
        );

        $this->pdfCombinator = new PdfCombinator($labelGenerator);
    }

    private static function fixture(string $fileName): string
    {
        return file_get_contents(self::FIXTURE_DIR . $fileName);
    }

    public function testSinglePdf2DocumentGetsCompatibleVersionHeader(): void
    {
        $label = self::fixture('parcel-de-shipping-label.pdf');

        $result = $this->pdfCombinator->combinePdfPages([$label]);

        self::assertSame('%PDF-1.7' . substr($label, 8), $result);
        self::assertCount(1, \Zend_Pdf::parse($result)->pages);
    }

    public function testMultiplePdf2DocumentsAreCombined(): void
    {
        $result = $this->pdfCombinator->combineB64PdfPages([
            base64_encode(self::fixture('parcel-de-shipping-label.pdf')),
            base64_encode(self::fixture('parcel-de-return-label.pdf')),
        ]);

        self::assertCount(2, \Zend_Pdf::parse($result)->pages);
    }

    /**
     * @return string[][]
     */
    public static function unchangedDocumentProvider(): array
    {
        return [
            'PDF 1.4' => ["%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF\n"],
            'PDF 1.7' => ["%PDF-1.7\n1 0 obj\n<<>>\nendobj\n%%EOF\n"],
            'PNG image' => [base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAf'
                . 'FcSJAAAADUlEQVR4nGNgYGD4DwABBAEAwS2OUAAAAABJRU5ErkJggg=='
            )],
            'empty' => [''],
        ];
    }

    #[DataProvider('unchangedDocumentProvider')]
    public function testSingleDocumentWithoutPdf2HeaderIsReturnedUnchanged(string $document): void
    {
        self::assertSame($document, $this->pdfCombinator->combinePdfPages([$document]));
    }

    public function testPdf2DocumentWithCrossReferenceStreamsStillFailsWithParserError(): void
    {
        try {
            $this->pdfCombinator->combinePdfPages([
                self::fixture('object-streams.pdf'),
                self::fixture('parcel-de-return-label.pdf'),
            ]);
            self::fail('Expected combining a PDF with cross-reference streams to fail.');
        } catch (RuntimeException $exception) {
            self::assertInstanceOf(\Zend_Pdf_Exception::class, $exception->getPrevious());
            self::assertSame('Cross-reference streams are not supported yet.', $exception->getPrevious()->getMessage());
        }
    }
}
