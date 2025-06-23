<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;

class Compatibility implements CompatibilityInterface
{
    /**
     * @var string
     */
    private $id = '';

    /**
     * @var string[]
     */
    private $subjects = [];

    /**
     * @var string
     */
    private $errorMessage = '';

    /**
     * @var string[]
     */
    private $masters = [];

    /**
     * @var string
     */
    private $triggerValue = '';

    /**
     * @var string
     */
    private $action;

    /**
     * @return string
     */
    #[\Override]
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    #[\Override]
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string[]
     */
    #[\Override]
    public function getSubjects(): array
    {
        return $this->subjects;
    }

    /**
     * @param string[] $subjects
     */
    #[\Override]
    public function setSubjects(array $subjects): void
    {
        $this->subjects = $subjects;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    #[\Override]
    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string[]
     */
    #[\Override]
    public function getMasters(): array
    {
        return $this->masters;
    }

    /**
     * @param string[] $masters
     */
    #[\Override]
    public function setMasters(array $masters): void
    {
        $this->masters = $masters;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getTriggerValue(): string
    {
        return $this->triggerValue;
    }

    /**
     * @param string $triggerValue
     */
    #[\Override]
    public function setTriggerValue(string $triggerValue): void
    {
        $this->triggerValue = $triggerValue;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    #[\Override]
    public function setAction(string $action)
    {
        $this->action = $action;
    }
}
