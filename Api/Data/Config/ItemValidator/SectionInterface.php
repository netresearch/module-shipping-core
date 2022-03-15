<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Config\ItemValidator;

use Magento\Framework\Phrase;

/**
 * A marker that adds an item validator to a section (i.e. a carrier).
 *
 * @api
 */
interface SectionInterface
{
    /**
     * The section identifier for sorting/grouping.
     */
    public function getSectionCode(): string;

    /**
     * The human-readable section name for display (optional).
     */
    public function getSectionName(): ?Phrase;
}
