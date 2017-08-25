<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel\Service\DataHandler;

/**
 * Class StringArrayHandler
 */
class StringArrayHandler
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
        // Send data to the array handler to array-ify
        $arrayHandler = new ArrayHandler();
        $val = $arrayHandler->commonHandler($val, $def);

        // Iterate through each of the items and send to handler
        $intHandler = new StringHandler();
        foreach ($val ?: array() as $key => $item) {
            $val[$key] = $intHandler->commonHandler($item);
        }

        // Return the value
        return $val;
    }
}
