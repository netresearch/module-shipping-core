<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingBox;

class Package implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $length;

    /**
     * @var float
     */
    private $height;

    /**
     * @var float
     */
    private $weight;

    /**
     * @var bool
     */
    private $isDefault;

    public function __construct(
        string $id,
        string $title,
        float $width,
        float $length,
        float $height,
        float $weight,
        bool $isDefault
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->width = $width;
        $this->length = $length;
        $this->height = $height;
        $this->weight = $weight;
        $this->isDefault = $isDefault;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
