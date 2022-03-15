<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Config\ItemValidator;

use Magento\Framework\Phrase;

/**
 * A marker that adds an item validator to a group (i.e. a carrier module) within a section.
 *
 * @api
 */
interface GroupInterface
{
    /**
     * The group identifier for sorting/grouping.
     */
    public function getGroupCode(): string;

    /**
     * The human-readable group name for display (optional).
     */
    public function getGroupName(): ?Phrase;
}
