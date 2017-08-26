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
use BuzzingPixel\DataModel\ModelCollection;

/**
 * Class CommandGroupModel
 * @property string $name
 * @property ModelCollection $commands
 */
class CommandGroupModel extends DataModel
{
    /**
     * Define attributes
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'name' => DataType::STRING,
            'commands' => DataType::COLLECTION,
        );
    }
}
