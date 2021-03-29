<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\GuestCart\GuestShippingAddressManagementInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

class GuestShippingAddressManagement
{
    /**
     * @var GuestShippingAddressManagementInterface
     */
    private $shippingAddressManagement;

    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $quoteIdResolver;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    public function __construct(
        GuestShippingAddressManagementInterface $shippingAddressManagement,
        MaskedQuoteIdToQuoteIdInterface $quoteIdResolver,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->shippingAddressManagement = $shippingAddressManagement;
        $this->quoteIdResolver = $quoteIdResolver;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Obtain the quote's shipping address.
     *
     * The original shipping address management throws a `NoSuchEntityException`,
     * even if a shipping address is assigned to the (virtual) quote. When clearing
     * the shipping settings selection, we need to obtain the shipping address no matter
     * if the quote is virtual or not.
     *
     * @see \Magento\Quote\Model\GuestCart\GuestShippingAddressManagement::get
     *
     * @param string $cartId
     * @return AddressInterface
     * @throws NoSuchEntityException
     */
    public function get(string $cartId): AddressInterface
    {
        try {
            $shippingAddress = $this->shippingAddressManagement->get($cartId);
        } catch (NoSuchEntityException $exception) {
            $cartId = $this->quoteIdResolver->execute($cartId);
            $quote = $this->quoteRepository->getActive($cartId);
            $shippingAddress = $quote->getShippingAddress();

            if (!$shippingAddress instanceof AddressInterface) {
                throw $exception;
            }
        }

        return $shippingAddress;
    }
}
