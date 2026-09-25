<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

use Magento\Framework\Exception\RuntimeException;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Netresearch\ShippingCore\Api\Util\PdfCombinatorInterface;

/**
 * Utility for merging PDF pages into a single PDF binary.
 */
class PdfCombinator implements PdfCombinatorInterface
{
    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * PdfCombinator constructor.
     *
     * @param LabelGenerator $labelGenerator
     */
    public function __construct(LabelGenerator $labelGenerator)
    {
        $this->labelGenerator = $labelGenerator;
    }

    /**
     * Combine an array of PDF binary content into one single PDF binary.
     *
     * @param string[] $pdfContent
     * @return string Combined binary PDF file.
     * @throws RuntimeException
     */
    #[\Override]
    public function combinePdfPages(array $pdfContent): string
    {
        $pdfContent = array_map($this->normalizePdfVersion(...), $pdfContent);

        if (empty($pdfContent)) {
            // no data given
            $pdfBinary = '';
        } elseif (count($pdfContent) < 2) {
            // exactly one file/page given, return as-is
            $pdfBinary = $pdfContent[0];
        } else {
            // multiple files/pages given, merge into one pdf file
            try {
                $pdfBinary = $this->labelGenerator->combineLabelsPdf($pdfContent)->render();
            } catch (\Zend_Pdf_Exception $exception) {
                throw new RuntimeException(__('Unable to process PDF content.'), $exception);
            }
        }

        return $pdfBinary;
    }

    /**
     * Combine an array of base64 encoded PDF content into one single PDF binary.
     *
     * @param string[] $pdfContent Base64 encoded PDF content
     * @return string Combined binary PDF file.
     * @throws RuntimeException
     */
    #[\Override]
    public function combineB64PdfPages(array $pdfContent): string
    {
        $pdfContent = array_map(function ($b64Data) {
            return base64_decode($b64Data);
        }, $pdfContent);

        return $this->combinePdfPages($pdfContent);
    }

    /**
     * Declare a PDF 2.x document as PDF 1.7, which Zend_Pdf accepts.
     *
     * Zend_Pdf rejects PDF 2.x by its version header alone. The replacement keeps the
     * byte length, so cross-reference offsets stay valid. Documents Zend_Pdf still cannot
     * read, e.g. with cross-reference streams, fail with the parser's own error.
     *
     * @param string $content
     * @return string
     */
    private function normalizePdfVersion(string $content): string
    {
        if (preg_match('/\A%PDF-2\.\d(?!\d)/', $content) !== 1) {
            return $content;
        }

        return substr_replace($content, '%PDF-1.7', 0, 8);
    }
}
