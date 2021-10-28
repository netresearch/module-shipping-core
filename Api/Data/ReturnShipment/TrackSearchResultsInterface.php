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
interface TrackSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get tracks list.
     *
     * @return TrackInterface[]
     */
    public function getItems();

    /**
     * Set tracks list.
     *
     * @param TrackInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
