<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\CheckoutManagementInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelectionManager;

class CheckoutManagement implements CheckoutManagementInterface
{
    /**
     * @var CheckoutDataProvider
     */
    private $checkoutDataProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ShippingAddressManagement
     */
    private $addressManagement;

    /**
     * @var QuoteSelectionManager
     */
    private $selectionManager;

    public function __construct(
        CheckoutDataProvider $checkoutDataProvider,
        StoreManagerInterface $storeManager,
        ShippingAddressManagement $addressManagement,
        QuoteSelectionManager $selectionManager
    ) {
        $this->checkoutDataProvider = $checkoutDataProvider;
        $this->storeManager = $storeManager;
        $this->addressManagement = $addressManagement;
        $this->selectionManager = $selectionManager;
    }

    /**
     * @param string $countryId
     * @param string $postalCode
     * @return ShippingDataInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    #[\Override]
    public function getCheckoutData(string $countryId, string $postalCode): ShippingDataInterface
    {
        $storeId = (int) $this->storeManager->getStore()->getId();

        return $this->checkoutDataProvider->getData($storeId, $countryId, $postalCode);
    }

    /**
     * @param int $cartId
     * @param SelectionInterface[] $shippingOptionSelections
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    #[\Override]
    public function updateShippingOptionSelections(int $cartId, array $shippingOptionSelections): void
    {
        $shippingAddressId = (int) $this->addressManagement->get($cartId)->getId();
        $this->selectionManager->save($shippingAddressId, $shippingOptionSelections);
    }
}
