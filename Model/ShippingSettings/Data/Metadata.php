<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;

class Metadata implements MetadataInterface
{
    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $logoUrl = '';

    /**
     * @var int
     */
    private $logoWidth = '';

    /**
     * @var string
     */
    private $color = '';

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[]
     */
    private $commentsBefore = [];

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[]
     */
    private $commentsAfter = [];

    /**
     * @var \Netresearch\ShippingCore\Api\Data\ShippingSettings\FootnoteInterface[]
     */
    private $footnotes = [];

    #[\Override]
    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }

    #[\Override]
    public function getLogoWidth(): int
    {
        return $this->logoWidth;
    }

    #[\Override]
    public function getTitle(): string
    {
        return $this->title;
    }

    #[\Override]
    public function getColor(): string
    {
        return $this->color;
    }

    #[\Override]
    public function getCommentsBefore(): array
    {
        return $this->commentsBefore;
    }

    #[\Override]
    public function getCommentsAfter(): array
    {
        return $this->commentsAfter;
    }

    #[\Override]
    public function getFootnotes(): array
    {
        return $this->footnotes;
    }

    #[\Override]
    public function setLogoUrl(string $logoUrl): void
    {
        $this->logoUrl = $logoUrl;
    }

    #[\Override]
    public function setLogoWidth(int $logoWidth): void
    {
        $this->logoWidth = $logoWidth;
    }

    #[\Override]
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    #[\Override]
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    #[\Override]
    public function setCommentsBefore(array $commentsBefore): void
    {
        $this->commentsBefore = $commentsBefore;
    }

    #[\Override]
    public function setCommentsAfter(array $commentsAfter): void
    {
        $this->commentsAfter = $commentsAfter;
    }

    #[\Override]
    public function setFootnotes(array $footnotes): void
    {
        $this->footnotes = $footnotes;
    }
}
