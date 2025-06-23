<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

use Monolog\LogRecord;

class ApiLogAnonymizer
{
    /**
     * Regular expressions for preg_replace
     *
     * @var string[]
     */
    private $patterns;

    /**
     * @var string
     */
    private $replacement;

    /**
     * ApiLogAnonymizer constructor.
     *
     * @param string[] $patterns
     * @param string $replacement
     */
    public function __construct(array $patterns = [], string $replacement = '[hidden]')
    {
        $this->patterns = $patterns;
        $this->replacement = $replacement;
    }

    /**
     * Strip sensitive strings from message by given property names.
     *
     * @param string $message
     * @return string
     */
    public function anonymize(string $message): string
    {
        return preg_replace_callback(
            $this->patterns,
            function ($matches) {
                $result = $matches[0];
                $found = count($matches);

                // exact search
                if ($found === 1) {
                    return $this->replacement;
                }

                // search with captured sub-patterns
                for ($i = $found; $i > 1; $i--) {
                    $result = str_replace($matches[$i - 1], $this->replacement, $result);
                }

                return $result;
            },
            $message
        );
    }

    /**
     * Processor for Monolog log records.
     *
     * @param LogRecord $record
     * @return LogRecord
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(message: $this->anonymize($record->message));
    }
}
