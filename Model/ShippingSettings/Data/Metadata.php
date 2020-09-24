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
    private $imageUrl = '';

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

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getCommentsBefore(): array
    {
        return $this->commentsBefore;
    }

    public function getCommentsAfter(): array
    {
        return $this->commentsAfter;
    }

    public function getFootnotes(): array
    {
        return $this->footnotes;
    }

    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function setCommentsBefore(array $commentsBefore): void
    {
        $this->commentsBefore = $commentsBefore;
    }

    public function setCommentsAfter(array $commentsAfter): void
    {
        $this->commentsAfter = $commentsAfter;
    }

    public function setFootnotes(array $footnotes): void
    {
        $this->footnotes = $footnotes;
    }
}
