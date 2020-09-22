<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\BulkShipment;

use Netresearch\ShippingCore\Api\Data\Pipeline\TrackRequest\TrackRequestInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;

/**
 * Service to cancel shipping labels.
 *
 * @api
 */
interface BulkLabelCancellationInterface
{
    /**
     * Cancel shipping labels for given cancellation requests.
     *
     * @param TrackRequestInterface[] $cancelRequests
     * @return TrackResponseInterface[]
     */
    public function cancelLabels(array $cancelRequests): array;
}
