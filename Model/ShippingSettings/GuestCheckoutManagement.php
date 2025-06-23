<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\GuestCheckoutManagementInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelectionManager;

class GuestCheckoutManagement implements GuestCheckoutManagementInterface
{
    /**
     * @var GuestShippingAddressManagement
     */
    private $addressManagement;

    /**
     * @var QuoteSelectionManager
     */
    private $selectionManager;

    public function __construct(
        GuestShippingAddressManagement $addressManagement,
        QuoteSelectionManager $selectionManager
    ) {
        $this->addressManagement = $addressManagement;
        $this->selectionManager = $selectionManager;
    }

    /**
     * Persist service selection.
     *
     * @param string $cartId
     * @param SelectionInterface[] $shippingOptionSelections
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    #[\Override]
    public function updateShippingOptionSelections(string $cartId, array $shippingOptionSelections): void
    {
        $shippingAddressId = (int) $this->addressManagement->get($cartId)->getId();
        $this->selectionManager->save($shippingAddressId, $shippingOptionSelections);
    }
}
