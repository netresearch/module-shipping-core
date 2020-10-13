<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\Data;

use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageItemInterface;

class PackageItem implements PackageItemInterface
{
    /**
     * @var int
     */
    private $orderItemId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $packageId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $qty;

    /**
     * @var float
     */
    private $weight;

    /**
     * @var float
     */
    private $price;

    /**
     * @var float|null
     */
    private $customsValue;

    /**
     * @var string
     */
    private $sku;

    /**
     * @var string
     */
    private $hsCode;

    /**
     * @var string
     */
    private $countryOfOrigin;

    public function __construct(
        int $orderItemId,
        int $productId,
        int $packageId,
        string $name,
        float $qty,
        float $weight,
        float $price,
        float $customsValue = null,
        string $sku = '',
        string $hsCode = '',
        string $countryOfOrigin = ''
    ) {
        $this->orderItemId = $orderItemId;
        $this->productId = $productId;
        $this->packageId = $packageId;
        $this->name = $name;
        $this->qty = $qty;
        $this->weight = $weight;
        $this->price = $price;
        $this->customsValue = $customsValue;
        $this->sku = $sku;
        $this->hsCode = $hsCode;
        $this->countryOfOrigin = $countryOfOrigin;
    }

    public function getOrderItemId(): int
    {
        return $this->orderItemId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getPackageId(): int
    {
        return $this->packageId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQty(): float
    {
        return $this->qty;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCustomsValue(): ?float
    {
        return $this->customsValue;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getHsCode(): string
    {
        return $this->hsCode;
    }

    public function getCountryOfOrigin(): string
    {
        return $this->countryOfOrigin;
    }
}
