<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Util;

use Magento\Framework\Exception\RuntimeException;

/**
 * Utility for merging PDF pages into a single PDF binary.
 */
interface PdfCombinatorInterface
{
    /**
     * Combine an array of PDF binary content into one single PDF binary.
     *
     * @param string[] $pdfContent
     * @return string Combined binary PDF file.
     * @throws RuntimeException
     */
    public function combinePdfPages(array $pdfContent): string;

    /**
     * Combine an array of base64 encoded PDF content into one single PDF binary.
     *
     * @param string[] $pdfContent Base64 encoded PDF content
     * @return string Combined binary PDF file.
     * @throws RuntimeException
     */
    public function combineB64PdfPages(array $pdfContent): string;
}
