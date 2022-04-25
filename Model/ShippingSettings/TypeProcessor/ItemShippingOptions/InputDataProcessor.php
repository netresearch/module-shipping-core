<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\ItemShippingOptions;

use Magento\Catalog\Model\Product\Attribute\Source\Countryofmanufacture;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Order\Shipment\Item;
use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\ItemShippingOptionsProcessorInterface;
use Netresearch\ShippingCore\Model\ItemAttribute\ShipmentItemAttributeReader;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Netresearch\ShippingCore\Model\Util\ShipmentItemFilter;

/**
 * Item input data processor
 *
 * Prefill item level inputs with
 * - a set of possible options (e.g. country list)
 * - predefined catalog data (e.g. item weight, country of manufacture)
 */
class InputDataProcessor implements ItemShippingOptionsProcessorInterface
{
    /**
     * @var ShipmentItemFilter
     */
    private $itemFilter;

    /**
     * @var ShipmentItemAttributeReader
     */
    private $itemAttributeReader;

    /**
     * @var Countryofmanufacture
     */
    private $countrySource;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var ShippingConfigInterface
     */
    private $config;

    public function __construct(
        ShipmentItemFilter $itemFilter,
        ShipmentItemAttributeReader $itemAttributeReader,
        Countryofmanufacture $countrySource,
        CommentInterfaceFactory $commentFactory,
        OptionInterfaceFactory $optionFactory,
        ShippingConfigInterface $config
    ) {
        $this->itemFilter = $itemFilter;
        $this->itemAttributeReader = $itemAttributeReader;
        $this->countrySource = $countrySource;
        $this->commentFactory = $commentFactory;
        $this->optionFactory = $optionFactory;
        $this->config = $config;
    }

    /**
     * @param int $orderItemId
     * @param ShipmentItemInterface[] $shipmentItems
     * @return ShipmentItemInterface
     * @throws \RuntimeException
     */
    private function getShipmentItemByOrderItemId(int $orderItemId, array $shipmentItems): ShipmentItemInterface
    {
        foreach ($shipmentItems as $shipmentItem) {
            if ((int) $shipmentItem->getOrderItemId() === $orderItemId) {
                return $shipmentItem;
            }
        }

        throw new \RuntimeException("Order item with ID $orderItemId not found.");
    }

    /**
     * Set options and values to inputs on item level.
     *
     * - Set possible options (e.g. country list)
     * - Set values from shipment item
     *
     * @param ShippingOptionInterface $shippingOption
     * @param ShipmentItemInterface|Item $shipmentItem
     */
    private function processInputs(ShippingOptionInterface $shippingOption, ShipmentItemInterface $shipmentItem): void
    {
        foreach ($shippingOption->getInputs() as $input) {
            switch ($input->getCode()) {
                // details
                case Codes::ITEM_INPUT_PRODUCT_ID:
                    $input->setDefaultValue((string) $shipmentItem->getProductId());
                    break;

                case Codes::ITEM_INPUT_SKU:
                    $input->setDefaultValue((string) $shipmentItem->getSku());
                    break;

                case Codes::ITEM_INPUT_PRODUCT_NAME:
                    $input->setDefaultValue((string) $shipmentItem->getName());
                    break;

                case Codes::ITEM_INPUT_PRICE:
                    $totalAmount = $shipmentItem->getOrderItem()->getBaseRowTotal()
                        - $shipmentItem->getOrderItem()->getBaseDiscountAmount()
                        + $shipmentItem->getOrderItem()->getBaseTaxAmount()
                        + $shipmentItem->getOrderItem()->getBaseDiscountTaxCompensationAmount();

                    $itemPrice = $totalAmount / $shipmentItem->getOrderItem()->getQtyOrdered();
                    $input->setDefaultValue((string) $itemPrice);
                    break;

                case Codes::ITEM_INPUT_WEIGHT:
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getWeightUnit($shipmentItem->getShipment()->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue((string) $shipmentItem->getWeight());
                    break;

                case Codes::ITEM_INPUT_QTY_TO_SHIP:
                    $input->setDefaultValue((string) $shipmentItem->getOrderItem()->getQtyOrdered());
                    break;

                case Codes::ITEM_INPUT_QTY:
                    $input->setDefaultValue((string) $shipmentItem->getQty());
                    break;

                // customs
                case Codes::ITEM_INPUT_HS_CODE:
                    $input->setDefaultValue($this->itemAttributeReader->getHsCode($shipmentItem));
                    break;

                case Codes::ITEM_INPUT_CUSTOMS_VALUE:
                    $totalAmount = $shipmentItem->getOrderItem()->getBaseRowTotal()
                        - $shipmentItem->getOrderItem()->getBaseDiscountAmount()
                        + $shipmentItem->getOrderItem()->getBaseTaxAmount()
                        + $shipmentItem->getOrderItem()->getBaseDiscountTaxCompensationAmount();
                    $itemPrice = $totalAmount / $shipmentItem->getOrderItem()->getQtyOrdered();
                    $input->setDefaultValue((string) $itemPrice);

                    $currency = $shipmentItem->getOrderItem()->getStore()->getBaseCurrency();
                    $currencySymbol = $currency->getCurrencySymbol() ?: $currency->getCode();
                    $comment = $this->commentFactory->create();
                    $comment->setContent($currencySymbol);
                    $input->setComment($comment);
                    break;

                case Codes::ITEM_INPUT_COUNTRY_OF_ORIGIN:
                    $input->setOptions(array_map(
                        function ($optionArray) {
                            $option = $this->optionFactory->create();
                            $option->setValue($optionArray['value']);
                            $option->setLabel($optionArray['label']);
                            return $option;
                        },
                        $this->countrySource->getAllOptions()
                    ));
                    $input->setDefaultValue($this->itemAttributeReader->getCountryOfManufacture($shipmentItem));
                    break;

                case Codes::ITEM_INPUT_EXPORT_DESCRIPTION:
                    $input->setDefaultValue($this->itemAttributeReader->getExportDescription($shipmentItem));
                    break;
            }
        }
    }

    /**
     * Set default values for item detail and item customs inputs from the shipment items.
     *
     * @param string $carrierCode
     * @param ItemShippingOptionsInterface[] $itemOptions
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return ItemShippingOptionsInterface[]
     */
    public function process(
        string $carrierCode,
        array $itemOptions,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): array {
        if (!$shipment) {
            return $itemOptions;
        }

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());

        foreach ($itemOptions as $itemOption) {
            try {
                $shipmentItem = $this->getShipmentItemByOrderItemId($itemOption->getItemId(), $items);
            } catch (\RuntimeException $exception) {
                continue;
            }

            foreach ($itemOption->getShippingOptions() as $shippingOption) {
                $this->processInputs($shippingOption, $shipmentItem);
            }
        }

        return $itemOptions;
    }
}
