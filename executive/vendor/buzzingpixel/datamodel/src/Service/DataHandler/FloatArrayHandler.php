<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel\Service\DataHandler;

/**
 * Class FloatArrayHandler
 */
class FloatArrayHandler
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
     * @param array $def
     * @return array
     */
    public function commonHandler($val, $def = array())
    {
        // Send data to the array handler to array-ify
        $arrayHandler = new ArrayHandler();
        $val = $arrayHandler->commonHandler($val, $def);

        // Iterate through each of the items and cast to handler
        $floatHandler = new FloatHandler();
        foreach ($val as $key => $item) {
            $val[$key] = $floatHandler->commonHandler($item);
        }

        // Return the value
        return $val;
    }

    /**
     * Validation handler
     * @param array $val
     * @param array $def
     * @return array
     */
    public function validationHandler($val, $def)
    {
        if (isset($def['required']) && $def['required'] && ! $val) {
            return array('This field is required');
        }

        $floatHandler = new FloatHandler();
        foreach ($val ?: array() as $item) {
            $errors = $floatHandler->validationHandler($item, $def);

            if ($errors) {
                return $errors;
            }
        }

        return array();
    }
}
