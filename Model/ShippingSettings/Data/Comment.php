<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface;

class Comment implements CommentInterface
{
    /**
     * @var string
     */
    private $content = '';

    /**
     * @var string|null
     */
    private $footnoteId;

    /**
     * @return string
     */
    #[\Override]
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    #[\Override]
    public function getFootnoteId(): ?string
    {
        return $this->footnoteId;
    }

    /**
     * @param string $content
     */
    #[\Override]
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param string $footnoteId
     */
    #[\Override]
    public function setFootnoteId(string $footnoteId): void
    {
        $this->footnoteId = $footnoteId;
    }
}
