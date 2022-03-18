<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Shipping;

use Netresearch\ShippingCore\Api\Shipping\ProductNameProviderInterface;

/**
 * @api
 */
class GetProductName
{
    /**
     * @var ProductNameProviderInterface[]
     */
    private $nameProviders;

    /**
     * @param ProductNameProviderInterface[] $nameProviders
     */
    public function __construct(array $nameProviders = [])
    {
        $this->nameProviders = $nameProviders;
    }

    public function execute(string $carrierCode, string $productCode): string
    {
        foreach ($this->nameProviders as $nameProvider) {
            if ($nameProvider->getCarrierCode() !== $carrierCode) {
                // product name requested for another carrier, continue.
                continue;
            }

            $name = $nameProvider->getName($productCode);
            if ($name) {
                // match found, return it.
                return $name;
            }
        }

        return '';
    }
}
