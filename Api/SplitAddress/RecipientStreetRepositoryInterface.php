<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\SplitAddress;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingCore\Api\Data\RecipientStreetInterface;

/**
 * @api
 */
interface RecipientStreetRepositoryInterface
{
    /**
     * Save recipient street object.
     *
     * @param RecipientStreetInterface $recipientStreet
     * @return RecipientStreetInterface
     * @throws CouldNotSaveException
     */
    public function save(RecipientStreetInterface $recipientStreet): RecipientStreetInterface;

    /**
     * Get recipient street by primary key.
     *
     * @param int $orderAddressId
     * @return RecipientStreetInterface
     * @throws NoSuchEntityException
     */
    public function get(int $orderAddressId): RecipientStreetInterface;
}
