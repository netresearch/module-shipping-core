<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Email\ReturnShipment;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Address;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MimeInterface;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterface;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Email\Container\ShipmentIdentity;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Api\ReturnShipment\DocumentDownloadInterface;
use Netresearch\ShippingCore\Model\Email\MixedPartMimeMessage;
use Symfony\Component\Mime\HtmlToTextConverter\DefaultHtmlToTextConverter;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;
use Symfony\Component\Mime\Part\Multipart\MixedPart;
use Symfony\Component\Mime\Part\TextPart;

class TransportBuilder
{
    private const XML_PATH_EMAIL_TEMPLATE = 'shipping/parcel_processing/return_label_template';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

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
     * @var DocumentDownloadInterface
     */
    private $download;

    /**
     * @var FactoryInterface
     */
    private $templateFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    private $mimePartInterfaceFactory;

    /**
     * @var MimeMessageInterfaceFactory
     */
    private $mimeMessageInterfaceFactory;

    /**
     * @var EmailMessageInterfaceFactory
     */
    private $emailMessageInterfaceFactory;

    /**
     * @var TransportInterfaceFactory
     */
    private $mailTransportFactory;

    /**
     * @var TrackInterface|null
     */
    private $track;

    /**
     * @var OrderInterface|null
     */
    private $order;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ShipmentIdentity $identityContainer,
        SenderResolverInterface $senderResolver,
        AddressConverter $addressConverter,
        DocumentDownloadInterface $download,
        FactoryInterface $templateFactory,
        MimePartInterfaceFactory $mimePartInterfaceFactory,
        MimeMessageInterfaceFactory $mimeMessageInterfaceFactory,
        EmailMessageInterfaceFactory $emailMessageInterfaceFactory,
        TransportInterfaceFactory $mailTransportFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->identityContainer = $identityContainer;
        $this->senderResolver = $senderResolver;
        $this->addressConverter = $addressConverter;
        $this->download = $download;
        $this->templateFactory = $templateFactory;
        $this->mimePartInterfaceFactory = $mimePartInterfaceFactory;
        $this->mimeMessageInterfaceFactory = $mimeMessageInterfaceFactory;
        $this->emailMessageInterfaceFactory = $emailMessageInterfaceFactory;
        $this->mailTransportFactory = $mailTransportFactory;

        $this->track = null;
        $this->order = null;
    }

    public function setOrder(OrderInterface $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function setTrack(TrackInterface $track): self
    {
        $this->track = $track;

        return $this;
    }

    private function getTemplate(): TemplateInterface
    {
        $templateIdentifier = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $this->order->getStoreId()
        );

        return $this->templateFactory->get($templateIdentifier);
    }

    /**
     * Build RFC 2046 compliant body part with text/plain alternative for HTML emails.
     *
     * For HTML templates, creates multipart/alternative with both text/plain and text/html.
     * For text templates, creates single text/plain part.
     *
     * @param TemplateInterface $template
     * @return AbstractPart Symfony TextPart (text-only) or AlternativePart (HTML with text fallback)
     */
    private function buildBodyPart(TemplateInterface $template): AbstractPart
    {
        // Template must be processed BEFORE reading the template type!
        $content = $template->processTemplate();
        // Note: Using TemplateTypesInterface constants (deprecated but still used by Magento core)
        $templateType = (int) $template->getType();

        if ($templateType === TemplateTypesInterface::TYPE_HTML) {
            // Generate text/plain alternative using Symfony's email-specific converter
            $converter = new DefaultHtmlToTextConverter();
            $plainContent = $converter->convert($content, 'utf-8');

            $plainPart = new TextPart($plainContent, 'utf-8', 'plain');
            $htmlPart = new TextPart($content, 'utf-8', 'html');

            // RFC 2046 Section 5.1.4: Plain text FIRST, HTML second
            return new AlternativePart($plainPart, $htmlPart);
        } else {
            // Text-only template
            return new TextPart($content, 'utf-8', 'plain');
        }
    }

    private function getAttachmentPart(DocumentInterface $document): MimePartInterface
    {
        return $this->mimePartInterfaceFactory->create([
            'content' => $document->getLabelData(),
            'fileName' => $this->download->getFileName($document, $this->track, $this->order),
            'disposition' => MimeInterface::DISPOSITION_ATTACHMENT,
            'encoding' => MimeInterface::ENCODING_BASE64,
            'type' => MimeInterface::TYPE_OCTET_STREAM
        ]);
    }

    /**
     * Build Symfony MixedPart with body (AlternativePart or TextPart) and document attachments.
     *
     * @param AbstractPart $bodyPart Symfony TextPart or AlternativePart from buildBodyPart()
     * @param DocumentInterface[] $documents
     * @return MixedPart
     * @see MixedPartMimeMessage For workaround explanation
     */
    private function buildMixedPart(AbstractPart $bodyPart, array $documents): MixedPart
    {
        $symfonyParts = [$bodyPart];  // Now directly AbstractPart, not wrapped in MimePartInterface

        foreach ($documents as $document) {
            $attachmentPart = $this->getAttachmentPart($document);
            $symfonyParts[] = $attachmentPart->getMimePart();
        }

        return new MixedPart(...$symfonyParts);
    }

    /**
     * Build the `subject`, `body` and `encoding` parts of the message.
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function buildMessageContent(): array
    {
        $template = $this->getTemplate();
        $template->setVars([
            'order_increment_id' => $this->order->getIncrementId(),
            'store_name' => $this->order->getStore()->getFrontendName(),
            'customer_name' => $this->order->getCustomerName(),
        ]);
        $template->setOptions([
            'area' => 'frontend',
            'store' => $this->order->getStoreId(),
        ]);

        $bodyPart = $this->buildBodyPart($template);
        $documents = $this->track->getDocuments();

        $mixedPart = $this->buildMixedPart($bodyPart, $documents);
        $mimeMessage = new MixedPartMimeMessage($mixedPart);

        return [
            'encoding' => 'utf-8',  // Symfony parts use utf-8 encoding
            'body' => $mimeMessage,
            'subject' => html_entity_decode((string) $template->getSubject(), ENT_QUOTES),
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
        $to = [$this->addressConverter->convert($this->order->getCustomerEmail(), $this->order->getCustomerName())];
        return ['from' => $from, 'to' => $to];
    }

    /**
     * @throws NoSuchEntityException
     * @throws MailException
     */
    public function build(): TransportInterface
    {
        if (!$this->track instanceof TrackInterface || !$this->order instanceof OrderInterface) {
            throw new \RuntimeException('Email builder is not ready, add order and track entities first.');
        }

        $this->identityContainer->setStore($this->order->getStore());

        $messageData = array_merge(
            $this->buildMessageContent(),
            $this->buildAddresses()
        );

        $this->order = null;
        $this->track = null;

        $message = $this->emailMessageInterfaceFactory->create($messageData);
        return $this->mailTransportFactory->create(['message' => $message]);
    }
}
