<?php

namespace Netresearch\ShippingCore\Api\PackagingPopup;


use Netresearch\ShippingCore\Model\PackagingPopup\RequestData;

/**
 * Prepare package data coming from the packaging popup in JSON format for the shipment request.
 *
 * The NR packaging popup sends a custom POST data structure to the NR controller. This request
 * data must be converted to a format that the Magento core controller understands.
 *
 * @api
 */
interface RequestDataConverterInterface
{
    /**
     * Convert the JSON data to a data object with typed accessors.
     *
     * @param string $json
     * @return RequestData
     */
    public function getData(string $json): RequestData;

    /**
     * Prepare the request params as suitable for the Magento Shipping save controller.
     *
     * @param string $json
     * @return mixed[]
     */
    public function getParams(string $json): array;
}
