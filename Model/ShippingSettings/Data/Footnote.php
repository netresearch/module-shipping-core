<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\FootnoteInterface;

class Footnote implements FootnoteInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var string[]
     */
    private $subjects = [];

    /**
     * @var bool
     */
    private $subjectsMustBeSelected = false;

    /**
     * @var bool
     */
    private $subjectsMustBeAvailable = false;

    /**
     * @return string
     */
    #[\Override]
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getContent(): string
    {
        return $this->content;
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
     * @return bool
     */
    #[\Override]
    public function isSubjectsMustBeSelected(): bool
    {
        return $this->subjectsMustBeSelected;
    }

    /**
     * @return bool
     */
    #[\Override]
    public function isSubjectsMustBeAvailable(): bool
    {
        return $this->subjectsMustBeAvailable;
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
     * @param string $content
     */
    #[\Override]
    public function setContent(string $content): void
    {
        $this->content = $content;
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
     * @param bool $subjectsMustBeSelected
     */
    #[\Override]
    public function setSubjectsMustBeSelected(bool $subjectsMustBeSelected): void
    {
        $this->subjectsMustBeSelected = $subjectsMustBeSelected;
    }

    /**
     * @param bool $subjectsMustBeAvailable
     */
    #[\Override]
    public function setSubjectsMustBeAvailable(bool $subjectsMustBeAvailable): void
    {
        $this->subjectsMustBeAvailable = $subjectsMustBeAvailable;
    }
}
