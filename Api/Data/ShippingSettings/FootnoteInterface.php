<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\ShippingSettings;

/**
 * Interface FootnoteInterface
 *
 * A DTO with rendering information for shipping option footnotes.
 *
 * @api
 */
interface FootnoteInterface
{
    /**
     * Retrieve the unique id of the shipping option footnote
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Retrieve the HTML content of the footnote
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Retrieve a list of shipping option codes the footnote references
     *
     * @see ShippingOptionInterface
     * @return string[]
     */
    public function getSubjects(): array;

    /**
     * If this returns true, the footnote should only be displayed once all subjects are selected
     *
     * @return bool
     */
    public function isSubjectsMustBeSelected(): bool;

    /**
     * If this returns true, the footnote should only be displayed once all subjects are available for selection
     *
     * @return bool
     */
    public function isSubjectsMustBeAvailable(): bool;

    /**
     * @param string $id
     *
     * @return void
     */
    public function setId(string $id): void;

    /**
     * @param string $content
     *
     * @return void
     */
    public function setContent(string $content): void;

    /**
     * @param string[] $subjects
     *
     * @return void
     */
    public function setSubjects(array $subjects): void;

    /**
     * @param bool $subjectsMustBeSelected
     *
     * @return void
     */
    public function setSubjectsMustBeSelected(bool $subjectsMustBeSelected): void;

    /**
     * @param bool $subjectsMustBeAvailable
     *
     * @return void
     */
    public function setSubjectsMustBeAvailable(bool $subjectsMustBeAvailable): void;
}
