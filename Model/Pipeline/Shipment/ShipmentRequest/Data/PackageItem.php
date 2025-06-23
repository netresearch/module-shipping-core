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
    private $countryOfOrigin;

    /**
     * @var string
     */
    private $exportDescription;

    /**
     * @var string
     */
    private $hsCode;

    public function __construct(
        int $orderItemId,
        int $productId,
        int $packageId,
        string $name,
        float $qty,
        float $weight,
        float $price,
        ?float $customsValue = null,
        string $sku = '',
        string $countryOfOrigin = '',
        string $exportDescription = '',
        string $hsCode = ''
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
        $this->countryOfOrigin = $countryOfOrigin;
        $this->exportDescription = $exportDescription;
        $this->hsCode = $hsCode;
    }

    #[\Override]
    public function getOrderItemId(): int
    {
        return $this->orderItemId;
    }

    #[\Override]
    public function getProductId(): int
    {
        return $this->productId;
    }

    #[\Override]
    public function getPackageId(): int
    {
        return $this->packageId;
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[\Override]
    public function getQty(): float
    {
        return $this->qty;
    }

    #[\Override]
    public function getWeight(): float
    {
        return $this->weight;
    }

    #[\Override]
    public function getPrice(): float
    {
        return $this->price;
    }

    #[\Override]
    public function getCustomsValue(): ?float
    {
        return $this->customsValue;
    }

    #[\Override]
    public function getSku(): string
    {
        return $this->sku;
    }

    #[\Override]
    public function getCountryOfOrigin(): string
    {
        return $this->countryOfOrigin;
    }

    #[\Override]
    public function getExportDescription(): string
    {
        return $this->exportDescription;
    }

    #[\Override]
    public function getHsCode(): string
    {
        return $this->hsCode;
    }
}
