<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\BulkShipment;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Netresearch\ShippingCore\Api\BulkShipment\OrderLoaderInterface;
use Netresearch\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Netresearch\ShippingCore\Model\Config\BatchProcessingConfig;
use Netresearch\ShippingCore\Model\LabelStatus\LabelStatusProvider;
use Psr\Log\LoggerInterface;
use Zend_Db_Exception;

class OrderLoader implements OrderLoaderInterface
{
    /**
     * @var BulkShipmentConfiguration
     */
    private $serviceConfig;

    /**
     * @var BatchProcessingConfig
     */
    private $batchConfig;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var LabelStatusProvider
     */
    private $labelStatusProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        BulkShipmentConfiguration $serviceConfig,
        BatchProcessingConfig $batchConfig,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        OrderRepositoryInterface $orderRepository,
        LabelStatusProvider $labelStatusProvider,
        LoggerInterface $logger
    ) {
        $this->serviceConfig = $serviceConfig;
        $this->batchConfig = $batchConfig;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->orderRepository = $orderRepository;
        $this->labelStatusProvider = $labelStatusProvider;
        $this->logger = $logger;
    }

    /**
     * Load all requested orders if they were placed with given carriers.
     *
     * @param int[] $orderIds
     * @param string[] $carrierCodes
     * @return OrderSearchResultInterface|OrderInterface[]
     */
    private function loadCollection(array $orderIds, array $carrierCodes)
    {
        $orderIdFilter = $this->filterBuilder->setField(OrderInterface::ENTITY_ID)
            ->setValue($orderIds)
            ->setConditionType('in')
            ->create();

        $carrierFilters = [];
        foreach ($carrierCodes as $carrierCode) {
            $carrierFilters[] = $this->filterBuilder->setField('main_table.shipping_method')
                ->setValue("{$carrierCode}_%")
                ->setConditionType('like')
                ->create();
        }

        // set simple filters
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter($orderIdFilter);
        $searchCriteria = $searchCriteriaBuilder->create();

        // add filter groups
        $this->filterGroupBuilder->setFilters($carrierFilters);
        $carrierFilterGroup = $this->filterGroupBuilder->create();

        $filterGroups = $searchCriteria->getFilterGroups();
        // add carrier filters as one OR group
        $filterGroups[] = $carrierFilterGroup;
        $searchCriteria->setFilterGroups($filterGroups);

        try {
            return $this->orderRepository->getList($searchCriteria);
        } catch (Zend_Db_Exception $exception) {
            $this->logger->error('Could not load orders for bulk processing.', ['exception' => $exception]);
            return [];
        }
    }

    public function load(array $orderIds): array
    {
        // obtain carrier codes that support bulk processing
        $carrierCodes = array_filter(
            $this->serviceConfig->getCarrierCodes(),
            function (string $carrierCode) {
                try {
                    return $this->serviceConfig->getBulkShipmentService($carrierCode);
                } catch (\RuntimeException $exception) {
                    return false;
                }
            }
        );

        // load selected orders if their carrier supports bulk processing
        $orders = $this->loadCollection($orderIds, $carrierCodes)->getItems();

        // consider label status
        $retryFailedShipments = $this->batchConfig->isRetryEnabled();
        $ordersLabelStatus = $this->labelStatusProvider->getLabelStatus($orderIds);
        $fnFilter = static function (Order $order) use ($retryFailedShipments, $ordersLabelStatus) {
            $labelStatus = $ordersLabelStatus[$order->getId()] ?? null;

            $try = in_array(
                $labelStatus,
                [
                    LabelStatusManagementInterface::LABEL_STATUS_PENDING,
                    LabelStatusManagementInterface::LABEL_STATUS_PARTIAL
                ],
                true
            );
            $retry = $retryFailedShipments && $labelStatus === LabelStatusManagementInterface::LABEL_STATUS_FAILED;

            return $try || $retry;
        };

        return array_filter($orders, $fnFilter);
    }
}
