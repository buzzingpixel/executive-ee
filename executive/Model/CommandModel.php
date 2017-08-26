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
 * Class CommandModel
 * @property string $name
 * @property string $class
 * @property string $method
 * @property string $description
 */
class CommandModel extends DataModel
{
    /**
     * Define attributes
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'name' => DataType::STRING,
            'class' => DataType::STRING,
            'method' => DataType::STRING,
            'description' => DataType::STRING,
        );
    }
}
