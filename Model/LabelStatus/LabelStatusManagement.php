<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\LabelStatus;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\GridInterface;
use Netresearch\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Netresearch\ShippingCore\Model\ResourceModel\LabelStatus\CollectionFactory;
use Psr\Log\LoggerInterface;

class LabelStatusManagement implements LabelStatusManagementInterface
{
    /**
     * @var CollectionFactory
     */
    private $labelStatusCollectionFactory;

    /**
     * @var LabelStatusFactory
     */
    private $labelStatusFactory;

    /**
     * @var LabelStatusRepository
     */
    private $labelStatusRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var GridInterface
     */
    private $orderGrid;

    /**
     * @var string[]
     */
    private $carrierCodes;

    public function __construct(
        CollectionFactory $labelStatusCollectionFactory,
        LabelStatusFactory $labelStatusFactory,
        LabelStatusRepository $labelStatusRepository,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        GridInterface $orderGrid,
        array $carrierCodes = []
    ) {
        $this->labelStatusCollectionFactory = $labelStatusCollectionFactory;
        $this->labelStatusFactory = $labelStatusFactory;
        $this->labelStatusRepository = $labelStatusRepository;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->orderGrid = $orderGrid;
        $this->carrierCodes = $carrierCodes;
    }

    /**
     * Update label status in persistent storage and sales order grid.
     *
     * @param OrderInterface|Order $order
     * @param LabelStatus $labelStatus
     * @return bool
     */
    private function updateLabelStatus(OrderInterface $order, LabelStatus $labelStatus): bool
    {
        $shippingMethod = strtok((string) $order->getShippingMethod(), '_');
        if (!in_array($shippingMethod, $this->carrierCodes, true)) {
            // carrier does not support label status
            return false;
        }

        try {
            $this->labelStatusRepository->save($labelStatus);

            // if asynchronous grid indexing is disabled, grid data must be refreshed explicitly.
            if (!$this->scopeConfig->getValue('dev/grid/async_indexing')) {
                $this->orderGrid->refresh($labelStatus->getOrderId());
            }
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return false;
        }

        return true;
    }

    /**
     * Set initial label status.
     *
     * When a new order is placed, and the assigned carrier has label status capabilities,
     * then the order's label status is set to an initial value ("pending").
     * The status will only be set if the order has no label status assigned yet.
     *
     * @see \Netresearch\ShippingCore\Observer\SetInitialLabelStatus::execute
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setInitialStatus(OrderInterface $order): bool
    {
        $labelStatusCollection = $this->labelStatusCollectionFactory->create();
        $labelStatusCollection->addFieldToFilter('order_id', $order->getEntityId());
        if ($labelStatusCollection->getSize() > 0) {
            // order has a label status already
            return true;
        }

        return $this->setLabelStatusPending($order);
    }

    /**
     * Set the order's label status to "pending".
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setLabelStatusPending(OrderInterface $order): bool
    {
        $labelStatus = $this->labelStatusFactory->create();
        $labelStatus->setData([
            LabelStatus::ORDER_ID => $order->getEntityId(),
            LabelStatus::STATUS_CODE => self::LABEL_STATUS_PENDING
        ]);

        return $this->updateLabelStatus($order, $labelStatus);
    }

    /**
     * Set the order's label status to "processed".
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setLabelStatusProcessed(OrderInterface $order): bool
    {
        $labelStatus = $this->labelStatusFactory->create();
        $labelStatus->setData([
            LabelStatus::ORDER_ID => $order->getEntityId(),
            LabelStatus::STATUS_CODE => self::LABEL_STATUS_PROCESSED
        ]);

        return $this->updateLabelStatus($order, $labelStatus);
    }

    /**
     * Set the order's label status to "failed".
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setLabelStatusFailed(OrderInterface $order): bool
    {
        $labelStatus = $this->labelStatusFactory->create();
        $labelStatus->setData([
            LabelStatus::ORDER_ID => $order->getEntityId(),
            LabelStatus::STATUS_CODE => self::LABEL_STATUS_FAILED
        ]);

        return $this->updateLabelStatus($order, $labelStatus);
    }

    /**
     * Set the order's label status to "partial".
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setLabelStatusPartial(OrderInterface $order): bool
    {
        $labelStatus = $this->labelStatusFactory->create();
        $labelStatus->setData([
            LabelStatus::ORDER_ID => $order->getEntityId(),
            LabelStatus::STATUS_CODE => self::LABEL_STATUS_PARTIAL
        ]);

        return $this->updateLabelStatus($order, $labelStatus);
    }
}
