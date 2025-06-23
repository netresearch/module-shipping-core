<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Pipeline\Shipment\ResponseProcessor;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\ShipmentCommentInterface;
use Magento\Sales\Api\Data\ShipmentCommentInterfaceFactory;
use Magento\Sales\Api\ShipmentCommentRepositoryInterface;
use Netresearch\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Netresearch\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;
use Psr\Log\LoggerInterface;

/**
 * Add order comment if bulk label creation gave an error.
 */
class AddShipmentComment implements ShipmentResponseProcessorInterface
{
    /**
     * @var ShipmentCommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var ShipmentCommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ShipmentCommentInterfaceFactory $commentFactory,
        ShipmentCommentRepositoryInterface $commentRepository,
        LoggerInterface $logger
    ) {
        $this->commentFactory = $commentFactory;
        $this->commentRepository = $commentRepository;
        $this->logger = $logger;
    }

    #[\Override]
    public function processResponse(array $labelResponses, array $errorResponses): void
    {
        array_walk(
            $errorResponses,
            function (ShipmentErrorResponseInterface $errorResponse) {
                $errors = $errorResponse->getErrors();
                $comment = $this->commentFactory->create(['data' => [
                    ShipmentCommentInterface::COMMENT => implode('; ', $errors),
                    ShipmentCommentInterface::PARENT_ID => $errorResponse->getSalesShipment()->getEntityId(),
                    ShipmentCommentInterface::IS_VISIBLE_ON_FRONT => false,
                    ShipmentCommentInterface::IS_CUSTOMER_NOTIFIED => false,
                ]]);

                try {
                    $this->commentRepository->save($comment);
                } catch (CouldNotSaveException $exception) {
                    $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
                }
            }
        );
    }
}
