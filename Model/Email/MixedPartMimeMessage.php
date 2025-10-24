<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Email;

use Magento\Framework\Mail\MimeInterface;
use Magento\Framework\Mail\MimeMessageInterface;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\Part\Multipart\MixedPart;

/**
 * Adapter to wrap Symfony MixedPart into Magento's MimeMessageInterface.
 *
 * This class exists as a workaround for Magento 2.4.8's MimeMessage bug (GitHub #39869)
 * where the core MimeMessage constructor only processes the first TextPart and discards
 * all DataPart attachments.
 *
 * Instead of using Magento's broken MimeMessage, we build Symfony's MixedPart directly
 * and wrap it in this adapter to satisfy Magento's MimeMessageInterface requirement.
 *
 * IMPORTANT: EmailMessage only uses getMimeMessage() for email construction.
 * The part inspection methods (getParts, getPartHeaders, getPartContent) are only needed
 * for email parsing/reading, not for sending. Therefore, stub implementations are correct.
 *
 * @see https://github.com/magento/magento2/issues/39869
 * @see \Magento\Framework\Mail\EmailMessage::__construct() Line 84 shows getMimeMessage() usage
 */
class MixedPartMimeMessage implements MimeMessageInterface
{
    /**
     * @var Message
     */
    private $symfonyMessage;

    /**
     * @param MixedPart $mixedPart Symfony MixedPart containing email body and attachments
     */
    public function __construct(MixedPart $mixedPart)
    {
        $this->symfonyMessage = new Message(null, $mixedPart);
    }

    /**
     * @inheritDoc
     *
     * Stub implementation: This method is only used for email parsing/inspection,
     * not for email construction. EmailMessage only calls getMimeMessage().
     */
    public function getParts(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     *
     * Returns true since our message contains multiple parts (body + attachments).
     */
    public function isMultiPart(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     *
     * Returns the complete MIME message as a string. Used for debugging and logging.
     */
    public function getMessage(string $endOfLine = MimeInterface::LINE_END): string
    {
        return str_replace("\r\n", $endOfLine, $this->symfonyMessage->toString());
    }

    /**
     * @inheritDoc
     *
     * Stub implementation: Part inspection methods are only used for email parsing,
     * not for sending. EmailMessage uses getMimeMessage() for email construction.
     */
    public function getPartHeadersAsArray(int $partNum): array
    {
        return [];
    }

    /**
     * @inheritDoc
     *
     * Stub implementation: Part inspection methods are only used for email parsing,
     * not for sending. EmailMessage uses getMimeMessage() for email construction.
     */
    public function getPartHeaders(int $partNum, string $endOfLine = MimeInterface::LINE_END): string
    {
        return '';
    }

    /**
     * @inheritDoc
     *
     * Stub implementation: Part inspection methods are only used for email parsing,
     * not for sending. EmailMessage uses getMimeMessage() for email construction.
     */
    public function getPartContent(int $partNum, string $endOfLine = MimeInterface::LINE_END): string
    {
        return '';
    }

    /**
     * @inheritDoc
     *
     * CRITICAL METHOD: This is the only method called by EmailMessage for email construction.
     * Returns the Symfony Message object containing our properly structured MIME content.
     *
     * @see \Magento\Framework\Mail\EmailMessage::__construct() Line 84
     */
    public function getMimeMessage(): Message
    {
        return $this->symfonyMessage;
    }
}
