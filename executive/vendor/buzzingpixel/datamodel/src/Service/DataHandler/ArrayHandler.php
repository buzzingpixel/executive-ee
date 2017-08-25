<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel\Service\DataHandler;

/**
 * Class BoolHandler
 */
class ArrayHandler
{
    /** @var string GET_HANDLER */
    const GET_HANDLER = 'commonHandler';

    /** @var string SET_HANDLER */
    const SET_HANDLER = 'commonHandler';

    /**
     * Common method to handle data
     * @param mixed $val
     * @param array $def
     * @return array
     */
    public function commonHandler($val, $def = array())
    {
        // Check if the incoming value is a string
        if (gettype($val) === 'string') {
            // If it is, find the explode operator
            $explodeOn = isset($def['explodeOn']) ? $def['explodeOn'] : '|';

            // Explode on the value to create our array
            $val = explode($explodeOn, $val);

        // Otherwise if the value is something other than an array
        } elseif (gettype($val) !== 'array') {
            // Create an array with the value
            $val = array(
                $val
            );
        }

        // Return the value
        return $val;
    }
}
