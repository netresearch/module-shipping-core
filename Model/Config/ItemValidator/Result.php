<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Config\ItemValidator;

use Magento\Framework\Phrase;
use Netresearch\ShippingCore\Api\Data\Config\ItemValidator\ResultInterface;

class Result implements ResultInterface
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var Phrase
     */
    private $name;

    /**
     * @var Phrase
     */
    private $message;

    /**
     * @var string
     */
    private $sectionCode;

    /**
     * @var Phrase|null
     */
    private $sectionName;

    /**
     * @var string
     */
    private $groupCode;

    /**
     * @var Phrase|null
     */
    private $groupName;

    public function __construct(
        string $status,
        Phrase $name,
        Phrase $message,
        string $sectionCode,
        ?Phrase $sectionName,
        string $groupCode,
        ?Phrase $groupName
    ) {
        $this->status = $status;
        $this->name = $name;
        $this->message = $message;
        $this->sectionCode = $sectionCode;
        $this->sectionName = $sectionName;
        $this->groupCode = $groupCode;
        $this->groupName = $groupName;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getName(): Phrase
    {
        return $this->name;
    }

    public function getMessage(): Phrase
    {
        return $this->message;
    }

    public function getSectionCode(): string
    {
        return $this->sectionCode;
    }

    public function getSectionName(): ?Phrase
    {
        return $this->sectionName;
    }

    public function getGroupCode(): string
    {
        return $this->groupCode;
    }

    public function getGroupName(): ?Phrase
    {
        return $this->groupName;
    }
}
