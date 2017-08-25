<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel\Service\DataHandler;

/**
 * Class FloatHandler
 */
class FloatHandler
{
    /** @var string GET_HANDLER */
    const GET_HANDLER = 'commonHandler';

    /** @var string SET_HANDLER */
    const SET_HANDLER = 'commonHandler';

    /** @var string VALIDATION_HANDLER */
    const VALIDATION_HANDLER = 'validationHandler';

    /**
     * Common method to handle data
     * @param mixed $val
     * @return float
     */
    public function commonHandler($val)
    {
        return (float) $val;
    }

    /**
     * Validation handler
     * @param mixed $val
     * @param array $def
     * @return array
     */
    public function validationHandler($val, $def)
    {
        if (isset($def['required']) && $def['required'] && ! $val) {
            return array('This field is required');
        }

        if (isset($def['min'])) {
            $min = (float) $def['min'];

            if ($val < $min) {
                return array("This field must not be less than {$min}");
            }
        }

        if (isset($def['max'])) {
            $max = (float) $def['max'];

            if ($val > $max) {
                return array("This field must not be more than {$max}");
            }
        }

        return array();
    }
}
