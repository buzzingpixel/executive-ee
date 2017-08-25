<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel\Service\DataHandler;

/**
 * Class DatetimeHandler
 */
class DatetimeHandler
{
    /** @var string GET_HANDLER */
    const GET_HANDLER = 'getHandler';

    /** @var string SET_HANDLER */
    const SET_HANDLER = 'setHandler';

    /** @var string VALIDATION_HANDLER */
    const VALIDATION_HANDLER = 'validationHandler';

    /**
     * Get handler
     * @param mixed $val
     * @return \DateTime
     */
    public function getHandler($val)
    {
        if (! $val instanceof \DateTime) {
            return null;
        }
        return $val;
    }

    /**
     * Set handler
     * @param mixed $val
     * @return \DateTime
     */
    public function setHandler($val)
    {
        if ($val instanceof \DateTime) {
            return $val;
        }

        $dateTime = null;

        if ($this->isValidTimeStamp($val)) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($val);
        }

        if (! $dateTime && is_string($val)) {
            try {
                $dateTime = new \DateTime($val);
            } catch (\Exception $e) {
                //
            }
        }

        return $dateTime;
    }

    /**
     * Validation handler
     * @param mixed $val
     * @param array $def
     * @return array $errors
     */
    public function validationHandler($val, $def)
    {
        if (isset($def['required']) &&
            $def['required'] &&
            ! $val instanceof \DateTime
        ) {
            return array('This field is required');
        }

        return array();
    }

    /**
     * Check if input is a valid timestamp
     * @param $timestamp
     * @return bool
     */
    private function isValidTimeStamp($timestamp)
    {
        if (! is_numeric($timestamp)) {
            return false;
        }

        $timestamp = (int) $timestamp;

        try {
            return ($timestamp <= PHP_INT_MAX) && ($timestamp >= ~PHP_INT_MAX);
        } catch (\Exception $e) {
            return false;
        }
    }
}
