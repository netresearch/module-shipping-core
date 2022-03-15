<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Api\Data\Config\ItemValidator;

use Magento\Framework\Phrase;

/**
 * The result of one config item validation.
 *
 * @api
 */
interface ResultInterface extends SectionInterface, GroupInterface
{
    public const ERROR = 'error'; // (x)
    public const NOTICE = 'notice'; // (!)
    public const INFO = 'info'; // (i)
    public const OK = 'ok'; // (/)

    /**
     * The check result
     */
    public function getStatus(): string;

    /**
     * The name (subject) of the check
     */
    public function getName(): Phrase;

    /**
     * A message explaining the check result
     */
    public function getMessage(): Phrase;
}
