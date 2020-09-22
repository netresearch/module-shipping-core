<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Shipment\Request;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestModifierInterface;
use Netresearch\ShippingCore\Model\Util\ShipmentItemFilter;

class RequestModifier implements RequestModifierInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

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

    /**
     * RequestModifier constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ShipmentItemFilter $itemFilter
     * @param RegionFactory $regionFactory
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ShipmentItemFilter $itemFilter,
        RegionFactory $regionFactory,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->itemFilter = $itemFilter;
        $this->regionFactory = $regionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Add general shipment request data.
     *
     * @param Request $shipmentRequest
     */
    private function modifyGeneralParams(Request $shipmentRequest)
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
    private function modifyReceiver(Request $shipmentRequest)
    {
        $address = $shipmentRequest->getOrderShipment()->getShippingAddress();
        $personName = trim($address->getFirstname() . ' ' . $address->getLastname());
        $addressStreet = trim($address->getStreetLine(1) . ' ' . $address->getStreetLine(2));
        $region = $address->getRegionCode() ? $address->getRegionCode() : $address->getRegion();

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
    private function modifyShipper(Request $shipmentRequest)
    {
        $storeId = $shipmentRequest->getOrderShipment()->getStoreId();
        $originStreet = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ADDRESS1,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $originStreet2 = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ADDRESS2,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $storeInfo = (array)$this->scopeConfig->getValue(
            'general/store_information',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $storeInfo = $this->dataObjectFactory->create(['data' => $storeInfo]);

        $shipperRegionCode = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_REGION_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = $this->regionFactory->create()->load($shipperRegionCode)->getCode();
        }

        $shipmentRequest->setShipperContactPersonName('');
        $shipmentRequest->setShipperContactPersonFirstName('');
        $shipmentRequest->setShipperContactPersonLastName('');
        $shipmentRequest->setShipperContactCompanyName($storeInfo->getData('name'));
        $shipmentRequest->setShipperContactPhoneNumber($storeInfo->getData('phone'));
        $shipmentRequest->setData('shipper_email', '');
        $shipmentRequest->setShipperAddressStreet(trim($originStreet . ' ' . $originStreet2));
        $shipmentRequest->setShipperAddressStreet1($originStreet);
        $shipmentRequest->setShipperAddressStreet2($originStreet2);
        $shipmentRequest->setShipperAddressCity(
            (string)$this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_CITY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );

        $shipmentRequest->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $shipmentRequest->setShipperAddressPostalCode(
            $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_ZIP,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );
        $shipmentRequest->setShipperAddressCountryCode(
            (string)$this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_COUNTRY_ID,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );
    }

    /**
     * Add package params and items data to shipment request.
     *
     * Cross-border params are omitted because the carrier decides whether the route requires customs data or not.
     *
     * @param Request $shipmentRequest
     * @throws LocalizedException
     */
    private function modifyPackage(Request $shipmentRequest): void
    {
        throw new LocalizedException(__('Not implemented yet.'));
    }

    /**
     * Add shipment request data using given shipment.
     *
     * The request modifier collects all additional data from defaults (config, product attributes)
     * during bulk label creation where no user input (packaging popup) is involved.
     *
     * @param Request $shipmentRequest
     * @throws LocalizedException
     */
    public function modify(Request $shipmentRequest): void
    {
        $this->modifyGeneralParams($shipmentRequest);
        $this->modifyReceiver($shipmentRequest);
        $this->modifyShipper($shipmentRequest);
        $this->modifyPackage($shipmentRequest);
    }
}
