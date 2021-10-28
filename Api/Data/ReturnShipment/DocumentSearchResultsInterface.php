<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ReturnShipment;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface DocumentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get documents list.
     *
     * @return DocumentInterface[]
     */
    public function getItems();

    /**
     * Set documents list.
     *
     * @param DocumentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
