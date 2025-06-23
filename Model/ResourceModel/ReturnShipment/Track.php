<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ResourceModel\ReturnShipment;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\TrackInterface;
use Netresearch\ShippingCore\Setup\Module\Constants;
use Psr\Log\LoggerInterface;

class Track extends AbstractDb
{
    /**
     * @var Document
     */
    private $documentResource;

    /**
     * @var DocumentCollectionFactory
     */
    private $documentCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        Document $documentResource,
        DocumentCollectionFactory $documentCollectionFactory,
        LoggerInterface $logger,
        $connectionName = null
    ) {
        $this->documentResource = $documentResource;
        $this->documentCollectionFactory = $documentCollectionFactory;
        $this->logger = $logger;

        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
    }

    /**
     * Init main table and primary key.
     *
     * @return void
     */
    #[\Override]
    protected function _construct()
    {
        $this->_init(
            Constants::TABLE_RETURN_SHIPMENT_TRACK,
            TrackInterface::ENTITY_ID
        );
    }

    /**
     * Persist documents with the track.
     *
     * @param AbstractModel $object
     * @return Track
     */
    #[\Override]
    protected function _afterSave(AbstractModel $object): self
    {
        parent::_afterSave($object);

        /** @var AbstractModel $document */
        foreach ($object->getData(TrackInterface::DOCUMENTS) as $document) {
            try {
                $document->setData(DocumentInterface::TRACK_ID, $object->getEntityId());
                $this->documentResource->save($document);
            } catch (\Exception $exception) {
                $message = sprintf(
                    'Error while saving document %s for return shipment %s: %s',
                    $document->getData(DocumentInterface::TITLE),
                    $object->getData(TrackInterface::TRACK_NUMBER),
                    $exception->getMessage()
                );
                $this->logger->error($message, ['exception' => $exception]);
            }
        }

        return $this;
    }

    #[\Override]
    protected function _afterLoad(AbstractModel $object): self
    {
        parent::_afterLoad($object);

        $documentCollection = $this->documentCollectionFactory->create();
        $documentCollection->addFieldToFilter(DocumentInterface::TRACK_ID, ['eq' => $object->getEntityId()]);
        $object->setData(TrackInterface::DOCUMENTS, $documentCollection->getItems());

        return $this;
    }
}
