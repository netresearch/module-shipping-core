<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment\Email;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
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

class TransportBuilder
{
    private const XML_PATH_EMAIL_TEMPLATE = 'shipping/parcel_processing/return_label_template';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

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

    private function getMainPart(TemplateInterface $template): MimePartInterface
    {
        // template must be processed BEFORE reading the template type!
        $content = $template->processTemplate();
        $mimePartType = ((int) $template->getType() === TemplateTypesInterface::TYPE_HTML)
            ? MimeInterface::TYPE_HTML
            : MimeInterface::TYPE_TEXT;

        return $this->mimePartInterfaceFactory->create(
            [
                'content' => $content,
                'type' =>  $mimePartType,
            ]
        );
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
     * Build the `subject`, `body` and `encoding` parts of the message.
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function buildMessageContent(): array
    {
        $template = $this->getTemplate();
        $template->setVars(
            [
                'order_increment_id' => $this->order->getIncrementId(),
                'store_name' => $this->order->getStore()->getFrontendName(),
                'customer_name' => $this->order->getCustomerName(),
            ]
        );
        $template->setOptions(
            [
                'area' => 'frontend',
                'store' => $this->order->getStoreId(),
            ]
        );

        $mainPart = $this->getMainPart($template);
        $mimeParts = array_map(
            function (DocumentInterface $document) {
                return $this->getAttachmentPart($document);
            },
            $this->track->getDocuments()
        );

        array_unshift($mimeParts, $mainPart);

        return [
            'encoding' => $mainPart->getCharset(),
            'body' => $this->mimeMessageInterfaceFactory->create(['parts' => $mimeParts]),
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
     * @throws LocalizedException
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
