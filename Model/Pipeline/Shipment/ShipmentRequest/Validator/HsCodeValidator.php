<?php

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Phrase;
use Magento\Shipping\Model\Shipment\Request;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentRequest\RequestValidatorInterface;

/**
 * Validate that no return shipment label is requested.
 */
class HsCodeValidator implements RequestValidatorInterface
{
    /**
     * Allowed HS code lengths when export notification is required.
     */
    private const ALLOWED_HSCODE_LENGTHS_EXPORT = [8, 10];

    /**
     * Allowed HS code lengths when export notification is not required.
     */
    private const ALLOWED_HSCODE_LENGTHS_GENERAL = [6, 8, 10];

    /**
     * Validate the shipment request.
     *
     * @param Request $shipmentRequest
     * @throws ValidatorException
     */
    #[\Override]
    public function validate(Request $shipmentRequest): void
    {
        $packages = $shipmentRequest->getData('packages') ?? [];
        foreach ($packages as $package) {
            $this->validatePackage($package);
        }
    }

    /**
     * Validate a single package.
     *
     * @param array $package
     * @throws ValidatorException
     */
    private function validatePackage(array $package): void
    {
        //no customs inputs - no need to validate
        if (empty($package['params']['customs'])) {
            return;
        }

        $exportNotification = $package['params']['customs']['electronicExportNotification'] ?? false;
        $items = $package['items'] ?? [];

        foreach ($items as $item) {
            $this->validateItemHsCode($item, (bool)$exportNotification);
        }
    }

    /**
     * Validate a single item's HS code based on export notification rules.
     *
     * @param array $item
     * @param bool $exportNotification
     * @throws ValidatorException
     */
    private function validateItemHsCode(array $item, bool $exportNotification): void
    {
        // Retrieve and validate HS code
        $hsCode = (string)($item['customs']['hsCode'] ?? '');
        $hsCodeLength = strlen($hsCode);

        // Get allowed lengths based on export notification
        $allowedLengths = $exportNotification
            ? self::ALLOWED_HSCODE_LENGTHS_EXPORT
            : self::ALLOWED_HSCODE_LENGTHS_GENERAL;

        if (!in_array($hsCodeLength, $allowedLengths, true)) {
            $errorMessage = $this->getErrorMessage($exportNotification);
            throw new ValidatorException($errorMessage);
        }
    }

    /**
     * Get the error message based on export notification rules.
     *
     * @param bool $exportNotification
     * @return string
     */
    private function getErrorMessage(bool $exportNotification): Phrase
    {
        if ($exportNotification) {
            return __('For shipments with an export declaration, customs tariff numbers must be 8 or 10 digits.');
        }

        return __('The tariff number must be numeric and 6, 8, or 10 digits long.');
    }
}
