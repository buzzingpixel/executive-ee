<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/construct/license
 * @link https://buzzingpixel.com/software/construct
 */

namespace BuzzingPixel\Executive;

use BuzzingPixel\DataModel\Model;

/**
 * Class BaseComponent
 */
abstract class BaseComponent extends Model
{
    /**
     * BaseComponent Constructor
     * @param mixed $properties
     */
    public function __construct(
        $properties = array()
    ) {
        // Run parent constructor
        parent::__construct($properties);

        // Run init
        call_user_func_array(
            array(
                $this,
                'init',
            ),
            func_get_args()
        );
    }

    /**
     * Initialize component
     */
    protected function init()
    {
    }
}
