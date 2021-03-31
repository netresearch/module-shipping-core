<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\PackagingPopup;

use Magento\Framework\Serialize\Serializer\Json;
use Netresearch\ShippingCore\Api\PackagingPopup\RequestDataConverterInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;

class RequestDataConverter implements RequestDataConverterInterface
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var RequestDataFactory
     */
    private $factory;

    public function __construct(Json $jsonSerializer, RequestDataFactory $factory)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->factory = $factory;
    }

    private function getShipmentComment(array $shipmentData): string
    {
        return $shipmentData['shipmentComment'] ?? '';
    }

    /**
     * Check if comment can be shown to customer.
     *
     * Note that shipment request data must not contain boolean false. It is expressed by a null value.
     *
     * @param mixed[] $shipmentData
     * @return true|null
     */
    private function isCommentNotificationEnabled(array $shipmentData): ?bool
    {
        return !empty($shipmentData['notifyCustomer']) ? $shipmentData['notifyCustomer'] : null;
    }

    /**
     * Check if shipment confirmation email should be sent.
     *
     * Note that shipment request data must not contain boolean false. It is expressed by a null value.
     *
     * @param mixed[] $shipmentData
     * @return true|null
     */
    private function isShipmentNotificationEnabled(array $shipmentData): ?bool
    {
        return !empty($shipmentData['sendEmail']) ? $shipmentData['sendEmail'] : null;
    }

    private function getPackages(array $packagesData): array
    {
        $packages = [];
        foreach ($packagesData as $data) {
            $packageId = $data['packageId'];
            $packageItems = [];

            $itemCustomsKey = Codes::ITEM_OPTION_CUSTOMS;
            foreach ($data['items'] as $itemId => $itemDetails) {
                // prepare the standard set of package data
                $itemCustomsValue = null;
                if (isset($itemDetails[$itemCustomsKey][Codes::ITEM_INPUT_CUSTOMS_VALUE])) {
                    $itemCustomsValue = $itemDetails[$itemCustomsKey][Codes::ITEM_INPUT_CUSTOMS_VALUE];
                    unset($itemDetails[$itemCustomsKey][Codes::ITEM_INPUT_CUSTOMS_VALUE]);
                }

                $detailsKey = Codes::ITEM_OPTION_DETAILS;
                $packageItem = [
                    'qty' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_QTY] ?? '1',
                    'customs_value' => $itemCustomsValue,
                    'price' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_PRICE] ?? '',
                    'name' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_PRODUCT_NAME] ?? '',
                    'weight' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_WEIGHT] ?? '',
                    'product_id' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_PRODUCT_ID] ?? '',
                    'order_item_id' => $itemId,
                    'sku' => $itemDetails[$detailsKey][Codes::ITEM_INPUT_SKU] ?? '',
                    'customs' => $itemDetails[$itemCustomsKey] ?? [],
                ];

                $packageItems[$itemId] = $packageItem;
            }

            $packageParams = $data['package'];

            $customsKey = Codes::PACKAGE_OPTION_CUSTOMS;
            $customsValue = $packageParams[$customsKey][Codes::PACKAGE_INPUT_CUSTOMS_VALUE] ?? null;
            $contentType = $packageParams[$customsKey][Codes::PACKAGE_INPUT_CONTENT_TYPE] ?? '';
            $contentTypeOther = $packageParams[$customsKey][Codes::PACKAGE_INPUT_EXPLANATION] ?? '';
            unset(
                $packageParams[$customsKey][Codes::PACKAGE_INPUT_CUSTOMS_VALUE],
                $packageParams[$customsKey][Codes::PACKAGE_INPUT_CONTENT_TYPE],
                $packageParams[$customsKey][Codes::PACKAGE_INPUT_EXPLANATION]
            );

            $detailsKey = Codes::PACKAGE_OPTION_DETAILS;
            $packages[$packageId] = [
                'params' => [
                    'shipping_product' => $packageParams[$detailsKey][Codes::PACKAGE_INPUT_PRODUCT_CODE] ?? '',
                    'container' => '',
                    'weight' => $packageParams[$detailsKey][Codes::PACKAGE_INPUT_WEIGHT] ?? '',
                    'weight_units' => $packageParams[$detailsKey][Codes::PACKAGE_INPUT_WEIGHT_UNIT] ?? '',
                    'length' => $packageParams[$detailsKey][Codes::PACKAGE_INPUT_LENGTH] ?? '',
                    'width' => $packageParams[$detailsKey][Codes::PACKAGE_INPUT_WIDTH] ?? '',
                    'height' => $packageParams[$detailsKey][Codes::PACKAGE_INPUT_HEIGHT] ?? '',
                    'dimension_units' => $packageParams[$detailsKey][Codes::PACKAGE_INPUT_SIZE_UNIT] ?? '',
                    'content_type' => $contentType,
                    'content_type_other' => $contentTypeOther,
                    'customs_value' => $customsValue,
                    'customs' => $packageParams[Codes::PACKAGE_OPTION_CUSTOMS] ?? [],
                    'services' => $data['service'] ?? [],
                ],
                'items' => $packageItems,
            ];

            // add any carrier-specific package params that are not included in the standard set
            $defaultPackageDetails = [
                Codes::PACKAGE_INPUT_PRODUCT_CODE,
                Codes::PACKAGE_INPUT_PACKAGING_ID,
                Codes::PACKAGE_INPUT_PACKAGING_WEIGHT,
                Codes::PACKAGE_INPUT_WEIGHT_UNIT,
                Codes::PACKAGE_INPUT_WEIGHT,
                Codes::PACKAGE_INPUT_SIZE_UNIT,
                Codes::PACKAGE_INPUT_LENGTH,
                Codes::PACKAGE_INPUT_WIDTH,
                Codes::PACKAGE_INPUT_HEIGHT,
            ];

            foreach ($packageParams[$detailsKey] as $packageDetail => $value) {
                if (!in_array($packageDetail, $defaultPackageDetails, true)) {
                    $packages[$packageId]['params'][$packageDetail] = $value;
                }
            }
        }

        return $packages;
    }

    /**
     * Obtain shipment item quantities, indexed by order item id.
     *
     * @param mixed[] $packagesData
     * @return int[]
     */
    private function getItemQuantities(array $packagesData): array
    {
        $quantities = [];

        foreach ($packagesData as $data) {
            foreach ($data['items'] as $itemId => $itemDetails) {
                if (isset($quantities[$itemId])) {
                    $quantities[$itemId] += $itemDetails[Codes::ITEM_OPTION_DETAILS][Codes::ITEM_INPUT_QTY] ?? '1';
                } else {
                    $quantities[$itemId] = $itemDetails[Codes::ITEM_OPTION_DETAILS][Codes::ITEM_INPUT_QTY] ?? '1';
                }
            }
        }

        return $quantities;
    }

    public function getData(string $json): RequestData
    {
        $data = $this->jsonSerializer->unserialize($json);
        // email confirmation flag, comment text
        $shipmentData = $data['shipment'] ?? [];
        // packaging popup contents
        $packagesData = $data['packages'] ?? [];

        return $this->factory->create([
            'packages' => $this->getPackages($packagesData),
            'shipmentItems' => $this->getItemQuantities($packagesData),
            'shipmentComment' => $this->getShipmentComment($shipmentData),
            'commentNotificationEnabled' => $this->isCommentNotificationEnabled($shipmentData),
            'shipmentNotificationEnabled' => $this->isShipmentNotificationEnabled($shipmentData),
        ]);
    }

    public function getParams(string $json): array
    {
        $requestData = $this->getData($json);

        return [
            'shipment' => [
                'comment_text' => $requestData->getShipmentComment(),
                'send_email' => $requestData->isShipmentNotificationEnabled(),
                'comment_customer_notify' => $requestData->isCommentNotificationEnabled(),
                'create_shipping_label' => '1',
                'items' => $requestData->getShipmentItems(),
            ],
            'packages' => $requestData->getPackages()
        ];
    }
}
