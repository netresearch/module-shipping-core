<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\SplitAddress;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Netresearch\ShippingCore\Api\Data\RecipientStreetInterface;
use Netresearch\ShippingCore\Api\Data\RecipientStreetInterfaceFactory;
use Netresearch\ShippingCore\Api\SplitAddress\RecipientStreetLoaderInterface;
use Netresearch\ShippingCore\Api\SplitAddress\RecipientStreetRepositoryInterface;
use Netresearch\ShippingCore\Model\Util\StreetSplitter;

/**
 * Load a GLS recipient street by given address.
 */
class RecipientStreetLoader implements RecipientStreetLoaderInterface
{
    /**
     * @var RecipientStreetRepositoryInterface
     */
    private $recipientStreetRepository;

    /**
     * @var RecipientStreetInterfaceFactory
     */
    private $recipientStreetFactory;

    /**
     * @var StreetSplitter
     */
    private $streetSplitter;

    public function __construct(
        RecipientStreetRepositoryInterface $recipientStreetRepository,
        RecipientStreetInterfaceFactory $recipientStreetFactory,
        StreetSplitter $streetSplitter
    ) {
        $this->recipientStreetRepository = $recipientStreetRepository;
        $this->recipientStreetFactory = $recipientStreetFactory;
        $this->streetSplitter = $streetSplitter;
    }

    public function load(OrderAddressInterface $address): RecipientStreetInterface
    {
        try {
            $recipientStreet = $this->recipientStreetRepository->get((int)$address->getEntityId());
        } catch (NoSuchEntityException $exception) {
            $recipientStreet = $this->recipientStreetFactory->create();
        }

        // update recipient street with street data from address argument
        $street = implode(', ', $address->getStreet());
        $addressParts = $this->streetSplitter->splitStreet($street);

        /** @var RecipientStreet $recipientStreet */
        $recipientStreet->setData([
            RecipientStreetInterface::ORDER_ADDRESS_ID => (int) $address->getEntityId(),
            RecipientStreetInterface::NAME => $addressParts['street_name'],
            RecipientStreetInterface::NUMBER => $addressParts['street_number'],
            RecipientStreetInterface::SUPPLEMENT => $addressParts['supplement'],
        ]);

        return $recipientStreet;
    }
}
