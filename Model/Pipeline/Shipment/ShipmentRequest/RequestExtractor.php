<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Shipment\Request;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageAdditionalInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageItemInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageItemInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\RecipientInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\RecipientInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\ShipperInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\ShipperInterfaceFactory;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestExtractor\ServiceOptionReaderInterface;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestExtractor\ServiceOptionReaderInterfaceFactory;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestExtractorInterface;
use Netresearch\ShippingCore\Api\Util\CountryCodeConverterInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Netresearch\ShippingCore\Model\SplitAddress\RecipientStreetRepository;
use Netresearch\ShippingCore\Model\Util\StreetSplitter;

/**
 * Request Extractor
 *
 * The original shipment request is a rather limited DTO with unstructured data (DataObject, array).
 * The extractor and its subtypes offer a well-defined interface to extract the request data and
 * isolates the toxic part of extracting unstructured array data from the shipment request.
 */
class RequestExtractor implements RequestExtractorInterface
{
    /**
     * @var Request
     */
    private $shipmentRequest;

    /**
     * @var StreetSplitter
     */
    private $streetSplitter;

    /**
     * @var CountryCodeConverterInterface
     */
    private $countryCodeConverter;

    /**
     * @var RecipientStreetRepository
     */
    private $recipientStreetRepository;

    /**
     * @var ShipperInterfaceFactory
     */
    private $shipperFactory;

    /**
     * @var RecipientInterfaceFactory
     */
    private $recipientFactory;

    /**
     * @var PackageInterfaceFactory
     */
    private $packageFactory;

    /**
     * @var PackageAdditionalInterfaceFactory
     */
    private $packageAdditionalFactory;

    /**
     * @var PackageItemInterfaceFactory
     */
    private $packageItemFactory;

    /**
     * @var ServiceOptionReaderInterface
     */
    private $serviceOptionReader;

    /**
     * @var ServiceOptionReaderInterfaceFactory
     */
    private $serviceOptionReaderFactory;

    /**
     * @var ShipperInterface
     */
    private $shipper;

    /**
     * @var RecipientInterface
     */
    private $recipient;

    /**
     * @var PackageInterface[]
     */
    private $packages;

    /**
     * @var PackageItemInterface[]
     */
    private $packageItems;

    public function __construct(
        Request $shipmentRequest,
        StreetSplitter $streetSplitter,
        CountryCodeConverterInterface $countryCodeConverter,
        RecipientStreetRepository $recipientStreetRepository,
        ShipperInterfaceFactory $shipperFactory,
        RecipientInterfaceFactory $recipientFactory,
        PackageInterfaceFactory $packageFactory,
        PackageAdditionalInterfaceFactory $packageAdditionalFactory,
        PackageItemInterfaceFactory $packageItemFactory,
        ServiceOptionReaderInterfaceFactory $serviceOptionReaderFactory
    ) {
        $this->shipmentRequest = $shipmentRequest;
        $this->streetSplitter = $streetSplitter;
        $this->countryCodeConverter = $countryCodeConverter;
        $this->recipientStreetRepository = $recipientStreetRepository;
        $this->shipperFactory = $shipperFactory;
        $this->recipientFactory = $recipientFactory;
        $this->packageFactory = $packageFactory;
        $this->packageAdditionalFactory = $packageAdditionalFactory;
        $this->packageItemFactory = $packageItemFactory;
        $this->serviceOptionReaderFactory = $serviceOptionReaderFactory;
    }

    /**
     * Obtain service option reader to read core specific service data.
     *
     * @return ServiceOptionReaderInterface
     */
    private function getServiceOptionReader(): ServiceOptionReaderInterface
    {
        if (empty($this->serviceOptionReader)) {
            $this->serviceOptionReader = $this->serviceOptionReaderFactory->create(
                ['shipmentRequest' => $this->shipmentRequest]
            );
        }

        return $this->serviceOptionReader;
    }

    public function isReturnShipmentRequest(): bool
    {
        return (bool) $this->shipmentRequest->getData('is_return');
    }

    public function getStoreId(): int
    {
        return (int) $this->shipmentRequest->getData('store_id');
    }

    public function getBaseCurrencyCode(): string
    {
        return (string) $this->shipmentRequest->getData('base_currency_code');
    }

    public function getOrder(): Order
    {
        return $this->shipmentRequest->getOrderShipment()->getOrder();
    }

    public function getShipment(): Shipment
    {
        return $this->shipmentRequest->getOrderShipment();
    }

    public function getShipper(): ShipperInterface
    {
        if (empty($this->shipper)) {
            $street = (string)$this->shipmentRequest->getShipperAddressStreet();
            $streetParts = $this->streetSplitter->splitStreet($street);
            $streetData = [
                'streetName' => $streetParts['street_name'],
                'streetNumber' => $streetParts['street_number'],
                'addressAddition' => $streetParts['supplement'],
            ];

            $shipperData = [
                'contactPersonName' => (string)$this->shipmentRequest->getShipperContactPersonName(),
                'contactPersonFirstName' => (string)$this->shipmentRequest->getShipperContactPersonFirstName(),
                'contactPersonLastName' => (string)$this->shipmentRequest->getShipperContactPersonLastName(),
                'contactCompanyName' => (string)$this->shipmentRequest->getShipperContactCompanyName(),
                'contactEmail' => (string)$this->shipmentRequest->getData('shipper_email'),
                'contactPhoneNumber' => (string)$this->shipmentRequest->getShipperContactPhoneNumber(),
                'street' => [
                    $this->shipmentRequest->getShipperAddressStreet1(),
                    $this->shipmentRequest->getShipperAddressStreet2(),
                ],
                'city' => (string) $this->shipmentRequest->getShipperAddressCity(),
                'state' => (string) $this->shipmentRequest->getShipperAddressStateOrProvinceCode(),
                'postalCode' => (string) $this->shipmentRequest->getShipperAddressPostalCode(),
                'countryCode' => $this->countryCodeConverter->convert(
                    (string) $this->shipmentRequest->getShipperAddressCountryCode()
                ),
            ];

            $shipperData = array_merge($shipperData, $streetData);
            $this->shipper = $this->shipperFactory->create($shipperData);
        }

        return $this->shipper;
    }

    public function getReturnRecipient(): ShipperInterface
    {
        return $this->getShipper();
    }

    public function getRecipient(): RecipientInterface
    {
        if (empty($this->recipient)) {
            try {
                $shippingAddressId = (int) $this->getOrder()->getData('shipping_address_id');
                $recipientStreet = $this->recipientStreetRepository->get($shippingAddressId);
                $streetData = [
                    'streetName' => $recipientStreet->getName(),
                    'streetNumber' => $recipientStreet->getNumber(),
                    'addressAddition' => $recipientStreet->getSupplement(),
                ];
            } catch (NoSuchEntityException $exception) {
                $streetData = [
                    'streetName' => '',
                    'streetNumber' => '',
                    'addressAddition' => '',
                ];
            }

            $recipientData = [
                'contactPersonName' => (string)$this->shipmentRequest->getRecipientContactPersonName(),
                'contactPersonFirstName' => (string)$this->shipmentRequest->getRecipientContactPersonFirstName(),
                'contactPersonLastName' => (string)$this->shipmentRequest->getRecipientContactPersonLastName(),
                'contactCompanyName' => (string)$this->shipmentRequest->getRecipientContactCompanyName(),
                'contactEmail' => (string)$this->shipmentRequest->getData('recipient_email'),
                'contactPhoneNumber' => (string)$this->shipmentRequest->getRecipientContactPhoneNumber(),
                'street' => [
                    $this->shipmentRequest->getRecipientAddressStreet1(),
                    $this->shipmentRequest->getRecipientAddressStreet2(),
                ],
                'city' => (string) $this->shipmentRequest->getRecipientAddressCity(),
                'state' => (string) $this->shipmentRequest->getRecipientAddressStateOrProvinceCode(),
                'postalCode' => (string) $this->shipmentRequest->getRecipientAddressPostalCode(),
                'countryCode' => $this->countryCodeConverter->convert(
                    (string) $this->shipmentRequest->getRecipientAddressCountryCode()
                ),
                'regionCode' => (string) $this->shipmentRequest->getData('recipient_address_region_code'),
            ];

            $recipientData = array_merge($recipientData, $streetData);
            $this->recipient = $this->recipientFactory->create($recipientData);
        }

        return $this->recipient;
    }

    public function getPackageWeight(): float
    {
        return (float) $this->shipmentRequest->getPackageWeight();
    }

    public function getPackages(): array
    {
        if (empty($this->packages)) {
            $this->packages = array_map(function (array $packageData) {
                $params = $packageData['params'];
                $package = $this->packageFactory->create([
                    'productCode' => $params['shipping_product'] ?? '',
                    'containerType' => $params['container'] ?? '',
                    'weightUom' => $params['weight_units'],
                    'dimensionsUom' => $params['dimension_units'],
                    'weight' => (float) $params['weight'],
                    'length' => isset($params['length']) ? (float) $params['length'] : null,
                    'width' => isset($params['width']) ? (float) $params['width'] : null,
                    'height' => isset($params['height']) ? (float) $params['height'] : null,
                    'customsValue' => isset($params['customs_value']) ? (float) $params['customs_value'] : null,
                    'contentType' => $params['content_type'] ?? '',
                    'contentExplanation' => $params['content_type_other'] ?? '',
                    'packageAdditional' => $this->packageAdditionalFactory->create(),
                ]);

                return $package;
            }, $this->shipmentRequest->getData('packages'));
        }

        $packageId = $this->shipmentRequest->getData('package_id');
        if ($packageId === null) {
            // no dedicated package requested, return all packages
            return $this->packages;
        }

        if (!isset($this->packages[$packageId])) {
            // requested package not found
            throw new LocalizedException(__('Package #%1 not found in shipment request.', $packageId));
        }

        return [$packageId => $this->packages[$packageId]];
    }

    public function getAllItems(): array
    {
        if (empty($this->packageItems)) {
            $allItems = [];
            $packages = $this->shipmentRequest->getData('packages');

            foreach ($packages as $packageId => $packageData) {
                $packageItems = array_map(function (array $itemData) use ($packageId) {
                    $packageItem = $this->packageItemFactory->create([
                        'orderItemId' => (int)$itemData['order_item_id'],
                        'productId' => (int)$itemData['product_id'],
                        'packageId' => (int)$packageId,
                        'name' => $itemData['name'],
                        'qty' => (float)$itemData['qty'],
                        'weight' => (float)$itemData['weight'],
                        'price' => (float)$itemData['price'],
                        'customsValue' => isset($itemData['customs_value']) ? (float)$itemData['customs_value'] : null,
                        'sku' => $itemData['sku'] ?? '',
                        'countryOfOrigin' => $this->countryCodeConverter->convert(
                            $itemData['customs']['countryOfOrigin'] ?? ''
                        ),
                        'exportDescription' => $itemData['customs']['exportDescription'] ?? '',
                        'hsCode' => $itemData['customs']['hsCode'] ?? '',
                    ]);

                    return $packageItem;
                }, $packageData['items']);

                $allItems[] = $packageItems;
            }

            $this->packageItems = array_merge(...$allItems);
        }

        return $this->packageItems;
    }

    public function getPackageItems(): array
    {
        $packageId = $this->shipmentRequest->getData('package_id');
        $items = array_filter(
            $this->getAllItems(),
            static function (PackageItemInterface $item) use ($packageId) {
                return ($packageId === $item->getPackageId());
            }
        );

        return $items;
    }

    public function isCashOnDelivery(): bool
    {
        return $this->getServiceOptionReader()->isServiceEnabled(Codes::SERVICE_OPTION_CASH_ON_DELIVERY);
    }

    public function getCodReasonForPayment(): string
    {
        return $this->getServiceOptionReader()->getServiceOptionValue(
            Codes::SERVICE_OPTION_CASH_ON_DELIVERY,
            Codes::SERVICE_INPUT_COD_REASON_FOR_PAYMENT
        );
    }

    public function isPickupLocationDelivery(): bool
    {
        return $this->getServiceOptionReader()->isServiceEnabled(Codes::SERVICE_OPTION_DELIVERY_LOCATION);
    }

    public function getDeliveryLocationType(): string
    {
        return $this->getServiceOptionReader()->getServiceOptionValue(
            Codes::SERVICE_OPTION_DELIVERY_LOCATION,
            Codes::SERVICE_INPUT_DELIVERY_LOCATION_TYPE
        );
    }

    public function getDeliveryLocationId(): string
    {
        return $this->getServiceOptionReader()->getServiceOptionValue(
            Codes::SERVICE_OPTION_DELIVERY_LOCATION,
            Codes::SERVICE_INPUT_DELIVERY_LOCATION_ID
        );
    }

    public function getDeliveryLocationNumber(): string
    {
        return $this->getServiceOptionReader()->getServiceOptionValue(
            Codes::SERVICE_OPTION_DELIVERY_LOCATION,
            Codes::SERVICE_INPUT_DELIVERY_LOCATION_NUMBER
        );
    }

    public function getDeliveryLocationCountryCode(): string
    {
        $countryCode = $this->getServiceOptionReader()->getServiceOptionValue(
            Codes::SERVICE_OPTION_DELIVERY_LOCATION,
            Codes::SERVICE_INPUT_DELIVERY_LOCATION_COUNTRY_CODE
        );

        return $this->countryCodeConverter->convert($countryCode);
    }

    public function getDeliveryLocationPostalCode(): string
    {
        return $this->getServiceOptionReader()->getServiceOptionValue(
            Codes::SERVICE_OPTION_DELIVERY_LOCATION,
            Codes::SERVICE_INPUT_DELIVERY_LOCATION_POSTAL_CODE
        );
    }

    public function getDeliveryLocationCity(): string
    {
        return $this->getServiceOptionReader()->getServiceOptionValue(
            Codes::SERVICE_OPTION_DELIVERY_LOCATION,
            Codes::SERVICE_INPUT_DELIVERY_LOCATION_CITY
        );
    }

    public function getDeliveryLocationStreet(): string
    {
        return $this->getServiceOptionReader()->getServiceOptionValue(
            Codes::SERVICE_OPTION_DELIVERY_LOCATION,
            Codes::SERVICE_INPUT_DELIVERY_LOCATION_STREET
        );
    }
}
