<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\LabelStatus;

use Magento\Framework\Exception\CouldNotSaveException;
use Netresearch\ShippingCore\Model\ResourceModel;

class LabelStatusRepository
{
    /**
     * @var ResourceModel\LabelStatus
     */
    private $resource;

    public function __construct(
        ResourceModel\LabelStatus $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Persist the label status object.
     *
     * @param LabelStatus $labelStatus
     * @return LabelStatus
     * @throws CouldNotSaveException
     */
    public function save(LabelStatus $labelStatus): LabelStatus
    {
        try {
            $this->resource->save($labelStatus);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save label status'), $exception);
        }

        return $labelStatus;
    }
}
