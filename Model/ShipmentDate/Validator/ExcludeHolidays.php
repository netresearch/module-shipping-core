<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShipmentDate\Validator;

use Magento\Framework\Exception\RuntimeException;
use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;
use Netresearch\ShippingCore\Api\ShipmentDate\DayValidatorInterface;
use Netresearch\ShippingCore\Model\Util\HolidayCalculator;
use Psr\Log\LoggerInterface;

class ExcludeHolidays implements DayValidatorInterface
{
    /**
     * @var ShippingConfigInterface
     */
    private $config;

    /**
     * @var HolidayCalculator
     */
    private $holidayCalculator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ShippingConfigInterface $config,
        HolidayCalculator $holidayCalculator,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->holidayCalculator = $holidayCalculator;
        $this->logger = $logger;
    }

    /**
     * Returns TRUE if the date is NOT a holiday otherwise FALSE.
     *
     * @param \DateTimeInterface $dateTime The date/time object to check
     * @param mixed $store The store to use for validation
     *
     * @return bool
     */
    #[\Override]
    public function validate(\DateTimeInterface $dateTime, $store = null): bool
    {
        try {
            return !$this->holidayCalculator->isHoliday(
                $dateTime,
                $this->config->getOriginCountry($store),
                $this->config->getOriginRegion($store)
            );
        } catch (RuntimeException $exception) {
            $this->logger->error($exception->getLogMessage());

            // failed to retrieve holiday information, must assume the date is not a holiday.
            return true;
        }
    }
}
