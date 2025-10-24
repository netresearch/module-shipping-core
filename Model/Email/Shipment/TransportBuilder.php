<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Email\Shipment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Address;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MimeInterface;
use Magento\Framework\Mail\MimePartInterface;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Email\Container\ShipmentIdentity;
use Netresearch\ShippingCore\Model\Email\MixedPartMimeMessage;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Component\Mime\Part\Multipart\MixedPart;
use Symfony\Component\Mime\Part\TextPart;

class TransportBuilder
{
    /**
     * @var ShipmentIdentity
     */
    private $identityContainer;

    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @var AddressConverter
     */
    private $addressConverter;

    /**
     * @var MimePartInterfaceFactory
     */
    private $mimePartInterfaceFactory;

    /**
     * @var EmailMessageInterfaceFactory
     */
    private $emailMessageInterfaceFactory;

    /**
     * @var TransportInterfaceFactory
     */
    private $mailTransportFactory;

    /**
     * @var ShipmentInterface|null
     */
    private $shipment;

    /**
     * @var string
     */
    private $receiverEmail;

    public function __construct(
        ShipmentIdentity $identityContainer,
        SenderResolverInterface $senderResolver,
        AddressConverter $addressConverter,
        MimePartInterfaceFactory $mimePartInterfaceFactory,
        EmailMessageInterfaceFactory $emailMessageInterfaceFactory,
        TransportInterfaceFactory $mailTransportFactory
    ) {
        $this->identityContainer = $identityContainer;
        $this->senderResolver = $senderResolver;
        $this->addressConverter = $addressConverter;
        $this->mimePartInterfaceFactory = $mimePartInterfaceFactory;
        $this->emailMessageInterfaceFactory = $emailMessageInterfaceFactory;
        $this->mailTransportFactory = $mailTransportFactory;

        $this->shipment = null;
        $this->receiverEmail = '';
    }

    public function setReceiverEmail(string $emailAddress): self
    {
        $this->receiverEmail = $emailAddress;

        return $this;
    }

    public function setShipment(ShipmentInterface $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }

    private function getAttachmentPart(): MimePartInterface
    {
        return $this->mimePartInterfaceFactory->create([
            'content' => $this->shipment->getShippingLabel(),
            'fileName' => sprintf('shipping_label_%s.pdf', $this->shipment->getIncrementId()),
            'disposition' => MimeInterface::DISPOSITION_ATTACHMENT,
            'encoding' => MimeInterface::ENCODING_BASE64,
            'type' => MimeInterface::TYPE_OCTET_STREAM
        ]);
    }

    /**
     * Build Symfony MixedPart with body (TextPart) and document attachment.
     *
     * @param AbstractPart $bodyPart Symfony TextPart for email body
     * @return MixedPart
     * @see MixedPartMimeMessage For workaround explanation
     */
    private function buildMixedPart(AbstractPart $bodyPart): MixedPart
    {
        $symfonyParts = [
            $bodyPart,  // Now directly AbstractPart, not wrapped in MimePartInterface
            $this->getAttachmentPart()->getMimePart()
        ];

        return new MixedPart(...$symfonyParts);
    }

    /**
     * Build the `subject`, `body` and `encoding` parts of the message.
     *
     * @return array
     */
    private function buildMessageContent(): array
    {
        $subject = __('Shipping label for shipment # %1', $this->shipment->getIncrementId());
        $content = __('Please find attached the shipping label for shipment # %1.', $this->shipment->getIncrementId());

        // Create Symfony TextPart directly for plain text email body
        $bodyPart = new TextPart((string) $content, 'utf-8', 'plain');

        $mixedPart = $this->buildMixedPart($bodyPart);
        $mimeMessage = new MixedPartMimeMessage($mixedPart);

        return [
            'encoding' => 'utf-8',  // Symfony parts use utf-8 encoding
            'body' => $mimeMessage,
            'subject' => html_entity_decode((string) $subject, ENT_QUOTES),
        ];
    }

    /**
     * Build the address data for the message.
     *
     * Note that CC and Reply-To are never set in the Magento sales emails,
     * neither do we. We do not support sending the return label emails
     * (bcc/copy-to) to the store owner either.
     *
     * @return Address[][]
     * @throws MailException
     */
    private function buildAddresses(): array
    {
        $sender = $this->senderResolver->resolve(
            $this->identityContainer->getEmailIdentity(),
            $this->identityContainer->getStore()->getId()
        );

        $from = [$this->addressConverter->convert($sender['email'], $sender['name'])];
        $to = [$this->addressConverter->convert($this->receiverEmail)];
        return ['from' => $from, 'to' => $to];
    }

    /**
     * @throws LocalizedException
     */
    public function build(): TransportInterface
    {
        if (!$this->shipment instanceof ShipmentInterface) {
            throw new \RuntimeException('Email builder is not ready, add shipment entity first.');
        }

        if (empty($this->receiverEmail)) {
            throw new \RuntimeException('Email builder is not ready, define the receiver email address first.');
        }

        $this->identityContainer->setStore($this->shipment->getStore());

        $messageData = array_merge(
            $this->buildMessageContent(),
            $this->buildAddresses()
        );

        $this->shipment = null;
        $this->receiverEmail = '';

        $message = $this->emailMessageInterfaceFactory->create($messageData);
        return $this->mailTransportFactory->create(['message' => $message]);
    }
}
