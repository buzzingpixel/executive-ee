<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Model;

use BuzzingPixel\DataModel\Model as DataModel;
use BuzzingPixel\DataModel\DataType;

/**
 * Class Model
 * @property int $myInteger
 */
class Model extends DataModel
{
    /**
     * Define attributes
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'myInteger' => DataType::INT,
        );
    }
}
