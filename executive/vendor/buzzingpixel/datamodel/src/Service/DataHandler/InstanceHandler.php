<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel\Service\DataHandler;

/**
 * Class InstanceHandler
 */
class InstanceHandler
{
    /** @var string GET_HANDLER */
    const GET_HANDLER = 'commonHandler';

    /** @var string SET_HANDLER */
    const SET_HANDLER = 'commonHandler';

    /**
     * Common method to handle data
     * @param mixed $val
     * @param array $def
     * @return mixed
     */
    public function commonHandler($val, $def = array())
    {
        // Make sure our instance def exists and the val is an instance of it
        if (! isset($def['expect']) || ! $val instanceof $def['expect']) {
            return null;
        }

        // Since the value is an instance of expected, return it
        return $val;
    }
}
