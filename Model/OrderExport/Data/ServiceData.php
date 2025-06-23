<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\OrderExport\Data;

use Netresearch\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface;
use Netresearch\ShippingCore\Api\Data\OrderExport\ServiceDataInterface;

class ServiceData implements ServiceDataInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var KeyValueObjectInterface[]
     */
    private $details;

    /**
     * ServiceData constructor.
     *
     * @param string $code
     * @param KeyValueObjectInterface[] $details
     */
    public function __construct(string $code, array $details)
    {
        $this->code = $code;
        $this->details = $details;
    }

    #[\Override]
    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return KeyValueObjectInterface[]
     */
    #[\Override]
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * @param KeyValueObjectInterface[] $details
     * @return $this
     */
    public function setDetails(array $details): self
    {
        $this->details = $details;

        return $this;
    }
}
