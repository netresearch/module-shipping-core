<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageItemInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\RecipientInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\ShipperInterface;

/**
 * Class RequestExtractor
 *
 * The original shipment request is a rather limited DTO with unstructured data (DataObject, array).
 * The extractor and its subtypes offer a well-defined interface to extract the request data and
 * isolates the toxic part of extracting unstructured array data from the shipment request.
 *
 * @api
 */
interface RequestExtractorInterface
{
    /**
     * Check if the given shipment request represents a return shipment.
     *
     * @return bool
     */
    public function isReturnShipmentRequest(): bool;

    /**
     * Extract the store ID as assigned to the current shipment (where the order was initially placed).
     *
     * @return int
     */
    public function getStoreId(): int;

    /**
     * Extract the base currency for the current shipment's store.
     *
     * @return string
     */
    public function getBaseCurrencyCode(): string;

    /**
     * Extract order from shipment request.
     *
     * @return Order
     */
    public function getOrder(): Order;

    /**
     * Extract shipment from shipment request.
     *
     * @return Shipment
     */
    public function getShipment(): Shipment;

    /**
     * Extract shipper from shipment request.
     *
     * @return ShipperInterface
     */
    public function getShipper(): ShipperInterface;

    /**
     * Extract return shipment receiver from shipment request.
     *
     * @return ShipperInterface
     */
    public function getReturnRecipient(): ShipperInterface;

    /**
     * Extract recipient from shipment request.
     *
     * @return RecipientInterface
     */
    public function getRecipient(): RecipientInterface;

    /**
     * Extract package weight from shipment request.
     *
     * @return float
     */
    public function getPackageWeight(): float;

    /**
     * Extract packages from shipment request.
     *
     * @return PackageInterface[]
     * @throws LocalizedException
     */
    public function getPackages(): array;

    /**
     * Obtain all items from all packages.
     *
     * @return PackageItemInterface[]
     */
    public function getAllItems(): array;

    /**
     * Obtain all items for the current package.
     *
     * @return PackageItemInterface[]
     */
    public function getPackageItems(): array;

    /**
     * Check if "cash on delivery" was chosen for the current shipment request.
     *
     * @return bool
     */
    public function isCashOnDelivery(): bool;

    /**
     * Obtain the "cashOnDelivery.reasonForPayment" value for the current package.
     *
     * @return string
     */
    public function getCodReasonForPayment(): string;

    /**
     * Check if "delivery location" was chosen for the current shipment request.
     *
     * @return bool
     */
    public function isPickupLocationDelivery(): bool;

    /**
     * Obtain the "deliveryLocation.type" value.
     *
     * @return string
     */
    public function getDeliveryLocationType(): string;

    /**
     * Obtain the "deliveryLocation.id" value.
     *
     * @return string
     */
    public function getDeliveryLocationId(): string;

    /**
     * Obtain the "deliveryLocation.number" value.
     *
     * @return string
     */
    public function getDeliveryLocationNumber(): string;

    /**
     * Obtain the "deliveryLocation.countryCode" value.
     *
     * @return string
     */
    public function getDeliveryLocationCountryCode(): string;

    /**
     * Obtain the "deliveryLocation.postalCode" value.
     *
     * @return string
     */
    public function getDeliveryLocationPostalCode(): string;

    /**
     * Obtain the "deliveryLocation.city" value.
     *
     * @return string
     */
    public function getDeliveryLocationCity(): string;

    /**
     * Obtain the "deliveryLocation.street" value.
     *
     * @return string
     */
    public function getDeliveryLocationStreet(): string;
}
