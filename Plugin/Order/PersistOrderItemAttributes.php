<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Netresearch\ShippingCore\Api\Data\OrderItemAttributesInterfaceFactory;
use Netresearch\ShippingCore\Model\ItemAttribute\OrderItemAttributesRepository;
use Psr\Log\LoggerInterface;

/**
 * Persist custom product attributes.
 */
class PersistOrderItemAttributes
{
    /**
     * @var OrderItemAttributesInterfaceFactory
     */
    private $orderItemAttributeFactory;

    /**
     * @var OrderItemAttributesRepository
     */
    private $orderItemAttributeRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        OrderItemAttributesInterfaceFactory $orderItemAttributeFactory,
        OrderItemAttributesRepository $orderItemAttributeRepository,
        LoggerInterface $logger
    ) {
        $this->orderItemAttributeFactory = $orderItemAttributeFactory;
        $this->orderItemAttributeRepository = $orderItemAttributeRepository;
        $this->logger = $logger;
    }

    /**
     * Persist additional order item properties.
     *
     * Shift order item's extension attributes to a new attribute entity
     * and save it with reference to the original item.
     *
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface $orderItem
     * @return OrderItemInterface
     */
    public function afterSave(
        OrderItemRepositoryInterface $subject,
        OrderItemInterface $orderItem
    ): OrderItemInterface {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            // no extension attributes where added to the item, ignore
            return $orderItem;
        }

        $countryOfManufacture = $extensionAttributes->getNrshippingCountryOfManufacture();
        $hsCode = $extensionAttributes->getNrshippingHsCode();

        if (!$countryOfManufacture && !$hsCode) {
            return $orderItem;
        }

        try {
            $orderItemAttribute = $this->orderItemAttributeFactory->create();
            $orderItemAttribute->setItemId((int) $orderItem->getItemId());
            $orderItemAttribute->setCountryOfManufacture($countryOfManufacture);
            $orderItemAttribute->setHsCode($hsCode);

            $this->orderItemAttributeRepository->save($orderItemAttribute);
        } catch (\Exception $ex) {
            $message = ($ex instanceof LocalizedException) ? $ex->getLogMessage() : $ex->getMessage();
            $this->logger->error($message, ['exception' => $ex]);
        }

        return $orderItem;
    }
}
