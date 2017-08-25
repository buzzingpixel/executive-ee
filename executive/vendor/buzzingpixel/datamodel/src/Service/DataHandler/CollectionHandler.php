<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel\Service\DataHandler;

use BuzzingPixel\DataModel\ModelCollection;

/**
 * Class InstanceHandler
 */
class CollectionHandler
{
    /** @var string GET_HANDLER */
    const GET_HANDLER = 'commonHandler';

    /** @var string SET_HANDLER */
    const SET_HANDLER = 'commonHandler';

    /** @var string AS_ARRAY_HANDLER */
    const AS_ARRAY_HANDLER = 'asArrayHandler';

    /**
     * Common method to handle data
     * @param mixed $val
     * @return ModelCollection
     */
    public function commonHandler($val)
    {
        // Make sure $val is an instance of ModelCollection
        if (! $val instanceof ModelCollection) {
            return null;
        }

        // Since the value is an instance of expected, return it
        return $val;
    }

    /**
     * Handle as array
     * @param mixed $val
     * @return array
     */
    public function asArrayHandler($val)
    {
        // Make sure $val is an instance of ModelCollection
        if (! $val instanceof ModelCollection) {
            return null;
        }

        // Return the model collection as an array
        return $val->asArray();
    }
}
