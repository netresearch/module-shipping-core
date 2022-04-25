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
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string[]
     */
    public function getSubjects(): array
    {
        return $this->subjects;
    }

    /**
     * @return bool
     */
    public function isSubjectsMustBeSelected(): bool
    {
        return $this->subjectsMustBeSelected;
    }

    /**
     * @return bool
     */
    public function isSubjectsMustBeAvailable(): bool
    {
        return $this->subjectsMustBeAvailable;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param string[] $subjects
     */
    public function setSubjects(array $subjects): void
    {
        $this->subjects = $subjects;
    }

    /**
     * @param bool $subjectsMustBeSelected
     */
    public function setSubjectsMustBeSelected(bool $subjectsMustBeSelected): void
    {
        $this->subjectsMustBeSelected = $subjectsMustBeSelected;
    }

    /**
     * @param bool $subjectsMustBeAvailable
     */
    public function setSubjectsMustBeAvailable(bool $subjectsMustBeAvailable): void
    {
        $this->subjectsMustBeAvailable = $subjectsMustBeAvailable;
    }
}
