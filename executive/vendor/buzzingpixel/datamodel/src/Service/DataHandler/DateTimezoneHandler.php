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
class DateTimezoneHandler
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
        if (! $val instanceof \DateTimeZone) {
            return null;
        }
        return $val;
    }

    /**
     * Set handler
     * @param mixed $val
     * @return \DateTimeZone
     */
    public function setHandler($val)
    {
        try {
            return new \DateTimeZone($val);
        } catch (\Exception $e) {
            return null;
        }
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
            ! $val instanceof \DateTimeZone
        ) {
            return array('This field is required');
        }

        return array();
    }
}
