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

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[]
     */
    public function getCommentsBefore(): array
    {
        return $this->commentsBefore;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[]
     */
    public function getCommentsAfter(): array
    {
        return $this->commentsAfter;
    }

    /**
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\FootnoteInterface[]
     */
    public function getFootnotes(): array
    {
        return $this->footnotes;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[] $commentsBefore
     *
     * @return void
     */
    public function setCommentsBefore(array $commentsBefore)
    {
        $this->commentsBefore = $commentsBefore;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[] $commentsAfter
     *
     * @return void
     */
    public function setCommentsAfter(array $commentsAfter)
    {
        $this->commentsAfter = $commentsAfter;
    }

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\FootnoteInterface[] $footnotes
     *
     * @return void
     */
    public function setFootnotes(array $footnotes)
    {
        $this->footnotes = $footnotes;
    }
}
