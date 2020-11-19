<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ShippingSettings;

/**
 * Interface MetadataInterface
 *
 * @api
 */
interface MetadataInterface
{
    /**
     * Get the url for a logo or image to display in the shipping options area.
     *
     * @return string
     */
    public function getLogoUrl(): string;

    /**
     * Get the display width for the logo in the shipping options area.
     *
     * @return int
     */
    public function getLogoWidth(): int;

    /**
     * Get the title to display in the to display in the shipping options area.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get the color to use in the shipping options area.
     *
     * @return string
     */
    public function getColor(): string;

    /**
     * Get a list of Comment objects to display at the top of the shipping options area.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[]
     */
    public function getCommentsBefore(): array;

    /**
     * Get a list of Comment objects to display at the bottom of the shipping options area.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[]
     */
    public function getCommentsAfter(): array;

    /**
     * Get a list of footnotes to display at the bottom of the shipping options area.
     *
     * @return \Netresearch\ShippingCore\Api\Data\ShippingSettings\FootnoteInterface[]
     */
    public function getFootnotes(): array;

    /**
     * @param string $logoUrl
     *
     * @return void
     */
    public function setLogoUrl(string $logoUrl): void;

    /**
     * @param int $logoWidth
     *
     * @return void
     */
    public function setLogoWidth(int $logoWidth): void;

    /**
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title): void;

    /**
     * @param string $color
     *
     * @return void
     */
    public function setColor(string $color): void;

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[] $commentsBefore
     *
     * @return void
     */
    public function setCommentsBefore(array $commentsBefore): void;

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[] $commentsAfter
     *
     * @return void
     */
    public function setCommentsAfter(array $commentsAfter): void;

    /**
     * @param \Netresearch\ShippingCore\Api\Data\ShippingSettings\FootnoteInterface[] $footnotes
     *
     * @return void
     */
    public function setFootnotes(array $footnotes): void;
}
