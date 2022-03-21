<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ReturnShipment\Provider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\UrlInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentInterface;
use Netresearch\ShippingCore\Api\Data\ReturnShipment\DocumentLinkInterfaceFactory;
use Netresearch\ShippingCore\Api\ReturnShipment\GetDocumentLinksInterface;
use Netresearch\ShippingCore\Model\ReturnShipment\DocumentRepository;

class GetDocumentLinks implements GetDocumentLinksInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var DocumentRepository
     */
    private $documentRepository;

    /**
     * @var DocumentLinkInterfaceFactory
     */
    private $linkFactory;

    /**
     * @param UrlInterface $urlBuilder
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param DocumentRepository $documentRepository
     * @param DocumentLinkInterfaceFactory $linkFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        DocumentRepository $documentRepository,
        DocumentLinkInterfaceFactory $linkFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->documentRepository = $documentRepository;
        $this->linkFactory = $linkFactory;
    }

    public function execute(int $orderId, int $trackId): array
    {
        $parentIdFilter = $this->filterBuilder
            ->setField(DocumentInterface::TRACK_ID)
            ->setValue($trackId)
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter($parentIdFilter)->create();

        return array_map(
            function (DocumentInterface $document) use ($orderId, $trackId) {
                $url = $this->urlBuilder->getUrl('nrshipping/order_rma/download', [
                    'order_id' => $orderId,
                    'track_id' => $trackId,
                    'document_id' => $document->getId(),
                ]);

                return $this->linkFactory->create([
                    'title' => $document->getTitle(),
                    'url' => $url,
                ]);
            },
            $this->documentRepository->getList($searchCriteria)->getItems()
        );
    }
}
