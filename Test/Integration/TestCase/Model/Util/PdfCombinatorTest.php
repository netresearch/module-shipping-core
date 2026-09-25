<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Test\Integration\TestCase\Model\Util;

use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\TestFramework\ObjectManager;
use Netresearch\ShippingCore\Api\Util\PdfCombinatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Label documents pass through the PDF combinator before Magento's label generator
 * processes them on shipment creation. Admin label printing processes the stored label again.
 */
class PdfCombinatorTest extends TestCase
{
    private const FIXTURE_DIR = __DIR__ . '/../../../../_files/pdf/';

    /**
     * @var PdfCombinatorInterface
     */
    private PdfCombinatorInterface $pdfCombinator;

    /**
     * @var LabelGenerator
     */
    private LabelGenerator $labelGenerator;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = ObjectManager::getInstance();
        $this->pdfCombinator = $objectManager->get(PdfCombinatorInterface::class);
        $this->labelGenerator = $objectManager->get(LabelGenerator::class);
    }

    private static function b64Fixture(string $fileName): string
    {
        return base64_encode(file_get_contents(self::FIXTURE_DIR . $fileName));
    }

    public function testSinglePdf2LabelCanBeStoredAndPrinted(): void
    {
        $labelContent = $this->pdfCombinator->combineB64PdfPages([
            self::b64Fixture('parcel-de-shipping-label.pdf'),
        ]);

        $shippingLabel = $this->labelGenerator->combineLabelsPdf([$labelContent])->render();
        $massPrint = $this->labelGenerator->combineLabelsPdf([$shippingLabel, $shippingLabel])->render();

        self::assertCount(1, \Zend_Pdf::parse($shippingLabel)->pages);
        self::assertCount(2, \Zend_Pdf::parse($massPrint)->pages);
    }

    public function testMultiplePdf2DocumentsCanBeStoredAndPrinted(): void
    {
        $labelContent = $this->pdfCombinator->combineB64PdfPages([
            self::b64Fixture('parcel-de-shipping-label.pdf'),
            self::b64Fixture('parcel-de-return-label.pdf'),
        ]);

        $shippingLabel = $this->labelGenerator->combineLabelsPdf([$labelContent])->render();
        $massPrint = $this->labelGenerator->combineLabelsPdf([$shippingLabel, $shippingLabel])->render();

        self::assertCount(2, \Zend_Pdf::parse($shippingLabel)->pages);
        self::assertCount(4, \Zend_Pdf::parse($massPrint)->pages);
    }
}
