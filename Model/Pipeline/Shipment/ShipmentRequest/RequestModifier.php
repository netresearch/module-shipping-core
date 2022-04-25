<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Shipping\Model\Shipment\Request;
use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestModifier\PackagingOptionReaderInterfaceFactory;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestModifierInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Netresearch\ShippingCore\Model\Util\ShipmentItemFilter;

class RequestModifier implements RequestModifierInterface
{
    /**
     * @var ShippingConfigInterface
     */
    private $config;

    /**
     * @var PackagingOptionReaderInterfaceFactory
     */
    private $packagingOptionReaderFactory;

    /**
     * @var ShipmentItemFilter
     */
    private $itemFilter;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        ShippingConfigInterface $config,
        PackagingOptionReaderInterfaceFactory $packagingOptionReaderFactory,
        ShipmentItemFilter $itemFilter,
        RegionFactory $regionFactory,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->config = $config;
        $this->packagingOptionReaderFactory = $packagingOptionReaderFactory;
        $this->itemFilter = $itemFilter;
        $this->regionFactory = $regionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Add general shipment request data.
     *
     * @param Request $shipmentRequest
     */
    private function modifyGeneralParams(Request $shipmentRequest): void
    {
        $orderShipment = $shipmentRequest->getOrderShipment();
        $order = $orderShipment->getOrder();
        $storeId = $orderShipment->getStoreId();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        $shippingMethod = $order->getShippingMethod(true)->getData('method');

        $shipmentRequest->setShippingMethod($shippingMethod);
        $shipmentRequest->setData('base_currency_code', $baseCurrencyCode);
        $shipmentRequest->setData('store_id', $storeId);
    }

    /**
     * Add recipient data to shipment request.
     *
     * @param Request $shipmentRequest
     */
    private function modifyReceiver(Request $shipmentRequest): void
    {
        $address = $shipmentRequest->getOrderShipment()->getShippingAddress();
        $personName = trim($address->getFirstname() . ' ' . $address->getLastname());
        $addressStreet = trim($address->getStreetLine(1) . ' ' . $address->getStreetLine(2));
        $region = $address->getRegionCode() ?: $address->getRegion();

        $shipmentRequest->setRecipientContactPersonName((string)$personName);
        $shipmentRequest->setRecipientContactPersonFirstName((string)$address->getFirstname());
        $shipmentRequest->setRecipientContactPersonLastName((string)$address->getLastname());
        $shipmentRequest->setRecipientContactCompanyName((string)$address->getCompany());
        $shipmentRequest->setData('recipient_contact_phone_number', (string)$address->getTelephone());
        $shipmentRequest->setData('recipient_email', (string)$address->getEmail());
        $shipmentRequest->setRecipientAddressStreet((string)$addressStreet);
        $shipmentRequest->setRecipientAddressStreet1((string)$address->getStreetLine(1));
        $shipmentRequest->setRecipientAddressStreet2((string)$address->getStreetLine(2));
        $shipmentRequest->setRecipientAddressCity((string)$address->getCity());
        $shipmentRequest->setRecipientAddressStateOrProvinceCode((string)$region);
        $shipmentRequest->setData('recipient_address_region_code', $address->getRegionCode());
        $shipmentRequest->setData('recipient_address_postal_code', $address->getPostcode());
        $shipmentRequest->setRecipientAddressCountryCode((string)$address->getCountryId());
    }

    /**
     * Add shipper data to shipment request.
     *
     * @param Request $shipmentRequest
     */
    private function modifyShipper(Request $shipmentRequest): void
    {
        $storeId = $shipmentRequest->getOrderShipment()->getStoreId();

        $originStreet = $this->config->getOriginStreet($storeId);
        $originCity = $this->config->getOriginCity($storeId);
        $originPostcode = $this->config->getOriginPostcode($storeId);
        $originCountry = $this->config->getOriginCountry($storeId);
        $storeInfo = $this->config->getStoreInformation($storeId);

        $shipperRegionCode = $this->config->getOriginRegion($storeId);
        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = $this->regionFactory->create()->load($shipperRegionCode)->getCode();
        }

        $shipmentRequest->setShipperContactPersonName('');
        $shipmentRequest->setShipperContactPersonFirstName('');
        $shipmentRequest->setShipperContactPersonLastName('');
        $shipmentRequest->setShipperContactCompanyName($storeInfo->getData('name'));
        $shipmentRequest->setShipperContactPhoneNumber($storeInfo->getData('phone'));
        $shipmentRequest->setData('shipper_email', '');
        $shipmentRequest->setShipperAddressStreet(trim($originStreet[0] . ' ' . $originStreet[1]));
        $shipmentRequest->setShipperAddressStreet1($originStreet[0]);
        $shipmentRequest->setShipperAddressStreet2($originStreet[1]);
        $shipmentRequest->setShipperAddressCity($originCity);

        $shipmentRequest->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $shipmentRequest->setShipperAddressPostalCode($originPostcode);
        $shipmentRequest->setShipperAddressCountryCode($originCountry);
    }

    /**
     * Add package params and items data to shipment request.
     *
     * Cross-border params are omitted because the carrier decides whether the route requires customs data or not.
     *
     * @param Request $shipmentRequest
     */
    private function modifyPackage(Request $shipmentRequest): void
    {
        // fixed package id on bulk shipments
        $packageId = 1;
        $shipment = $shipmentRequest->getOrderShipment();

        $packagingOptionReader = $this->packagingOptionReaderFactory->create(['shipment' => $shipment]);

        try {
            $customs = $packagingOptionReader->getPackageOptionValues(Codes::PACKAGE_OPTION_CUSTOMS);
        } catch (\RuntimeException $exception) {
            $customs = [];
        }

        $customsValue = $customs[Codes::PACKAGE_INPUT_CUSTOMS_VALUE] ?? null;
        $contentType = $customs[Codes::PACKAGE_INPUT_CONTENT_TYPE] ?? '';
        $explanation = $customs[Codes::PACKAGE_INPUT_EXPLANATION] ?? '';
        unset(
            $customs[Codes::PACKAGE_INPUT_CUSTOMS_VALUE],
            $customs[Codes::PACKAGE_INPUT_CONTENT_TYPE],
            $customs[Codes::PACKAGE_INPUT_EXPLANATION]
        );

        $productCode = $packagingOptionReader->getPackageOptionValue(
            Codes::PACKAGE_OPTION_DETAILS,
            Codes::PACKAGE_INPUT_PRODUCT_CODE
        );

        $packageItems = [];
        $packageParams = [
            'shipping_product' => $productCode,
            'container' => '',
            'weight' => $packagingOptionReader->getPackageOptionValue(
                Codes::PACKAGE_OPTION_DETAILS,
                Codes::PACKAGE_INPUT_WEIGHT
            ),
            'weight_units' => $packagingOptionReader->getPackageOptionValue(
                Codes::PACKAGE_OPTION_DETAILS,
                Codes::PACKAGE_INPUT_WEIGHT_UNIT
            ),
            'length' => $packagingOptionReader->getPackageOptionValue(
                Codes::PACKAGE_OPTION_DETAILS,
                Codes::PACKAGE_INPUT_LENGTH
            ),
            'width' => $packagingOptionReader->getPackageOptionValue(
                Codes::PACKAGE_OPTION_DETAILS,
                Codes::PACKAGE_INPUT_WIDTH
            ),
            'height' => $packagingOptionReader->getPackageOptionValue(
                Codes::PACKAGE_OPTION_DETAILS,
                Codes::PACKAGE_INPUT_HEIGHT
            ),
            'dimension_units' => $packagingOptionReader->getPackageOptionValue(
                Codes::PACKAGE_OPTION_DETAILS,
                Codes::PACKAGE_INPUT_SIZE_UNIT
            ),
            'content_type' => $contentType,
            'content_type_other' => $explanation,
            'customs_value' => $customsValue,
            'customs' => $customs,
            'services' => $packagingOptionReader->getServiceValues(),
        ];

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());
        foreach ($items as $item) {
            $orderItemId = (int) $item->getOrderItemId();
            try {
                $itemCustoms = $packagingOptionReader->getItemOptionValues(
                    $orderItemId,
                    Codes::ITEM_OPTION_CUSTOMS
                );
            } catch (\RuntimeException $exception) {
                $itemCustoms = [];
            }

            $itemCustomsValue = $itemCustoms['customsValue'] ?? null;
            $packageItem = [
                'qty' => $packagingOptionReader->getItemOptionValue($orderItemId, 'itemDetails', 'qty'),
                'price' => $packagingOptionReader->getItemOptionValue($orderItemId, 'itemDetails', 'price'),
                'name' => $packagingOptionReader->getItemOptionValue($orderItemId, 'itemDetails', 'productName'),
                'weight' => $packagingOptionReader->getItemOptionValue($orderItemId, 'itemDetails', 'weight'),
                'product_id' => $packagingOptionReader->getItemOptionValue($orderItemId, 'itemDetails', 'productId'),
                'sku' => $packagingOptionReader->getItemOptionValue($orderItemId, 'itemDetails', 'sku'),
                'order_item_id' => $orderItemId,
                'customs_value' => $itemCustomsValue,
                'customs' => $itemCustoms,
            ];
            $packageItems[$orderItemId] = $packageItem;
        }

        $packages = [
            $packageId => [
                'params' => $packageParams,
                'items' => $packageItems,
            ]
        ];

        $shipmentRequest->setData('packages', $packages);
        $shipmentRequest->setData('package_id', $packageId);
        $shipmentRequest->setData('package_items', $packageItems);
        $shipmentRequest->setData('package_params', $this->dataObjectFactory->create(['data' => $packageParams]));

        $shipment->setPackages($packages);
    }

    /**
     * Add shipment request data using given shipment.
     *
     * The request modifier collects all additional data from defaults (config, product attributes)
     * during bulk label creation where no user input (packaging popup) is involved.
     *
     * @param Request $shipmentRequest
     */
    public function modify(Request $shipmentRequest): void
    {
        $this->modifyGeneralParams($shipmentRequest);
        $this->modifyReceiver($shipmentRequest);
        $this->modifyShipper($shipmentRequest);
        $this->modifyPackage($shipmentRequest);
    }
}
