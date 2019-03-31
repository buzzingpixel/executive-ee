<?php

declare(strict_types=1);

namespace buzzingpixel\executive\traits;

use DateTime;
use DateTimeZone;
use Throwable;
use const PHP_INT_MAX;
use function date_default_timezone_get;
use function is_numeric;
use function is_string;

trait DateTimeTrait
{
    /**
     * Creates a DateTime object from the incoming value
     */
    public function createDateTimeFromVal($val) : DateTime
    {
        if ($val instanceof DateTime) {
            return $val;
        }

        $dateTime = null;

        $dateTimeZone = new DateTimeZone(date_default_timezone_get());

        if ($this->isValidTimeStamp($val)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $dateTime = new DateTime();
            $dateTime->setTimezone($dateTimeZone);
            $dateTime->setTimestamp($val);
        }

        if (! $dateTime && is_string($val)) {
            try {
                $dateTime = new DateTime($val);
            } catch (Throwable $e) {
            }
        }

        if (! $dateTime) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $dateTime = new DateTime();
            $dateTime->setTimestamp(0);
            $dateTime->setTimezone($dateTimeZone);
        }

        return $dateTime;
    }

    /**
     * Checks if value is valid timestamp
     */
    public function isValidTimeStamp($val) : bool
    {
        if (! is_numeric($val)) {
            return false;
        }

        $timestamp = (int) $val;

        try {
            return ($timestamp <= PHP_INT_MAX) && ($timestamp >= ~PHP_INT_MAX);
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Gets the Database formatting string
     */
    public function getDatabaseFormatString() : string
    {
        return 'Y-m-d H:i:s';
    }
}
