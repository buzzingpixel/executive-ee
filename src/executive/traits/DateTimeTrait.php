<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\traits;

use DateTime;
use Exception;
use DateTimeZone;

/**
 * Class DateTimeTrait
 */
trait DateTimeTrait
{
    /**
     * Creates a DateTime object from the incoming value
     * @param mixed $val
     * @return DateTime
     */
    public function createDateTimeFromVal($val): DateTime
    {
        if ($val instanceof DateTime) {
            return $val;
        }

        $dateTime = null;

        $dateTimeZone = new DateTimeZone(date_default_timezone_get());

        if ($this->isValidTimeStamp($val)) {
            $dateTime = new DateTime();
            $dateTime->setTimezone($dateTimeZone);
            $dateTime->setTimestamp($val);
        }

        if (! $dateTime && \is_string($val)) {
            try {
                $dateTime = new DateTime($val);
            } catch (Exception $e) {
            }
        }

        if (! $dateTime) {
            $dateTime = new DateTime();
            $dateTime->setTimestamp(0);
            $dateTime->setTimezone($dateTimeZone);
        }

        return $dateTime;
    }

    /**
     * Checks if value is valid timestamp
     * @param $val
     * @return bool
     */
    public function isValidTimeStamp($val): bool
    {
        if (! is_numeric($val)) {
            return false;
        }

        $timestamp = (int) $val;

        try {
            return ($timestamp <= PHP_INT_MAX) && ($timestamp >= ~PHP_INT_MAX);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Gets the Database formatting string
     * @return string
     */
    public function getDatabaseFormatString(): string
    {
        return 'Y-m-d H:i:s';
    }
}
