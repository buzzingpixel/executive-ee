<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Service;

use BuzzingPixel\Executive\BaseComponent;
use BuzzingPixel\Executive\Model\ArgumentsModel;

/**
 * Class ArgsService
 */
class ArgsService extends BaseComponent
{
    /**
     * Parse raw arguments
     * @param array $rawArgs
     * @return ArgumentsModel
     */
    public function parseRawArgs($rawArgs)
    {
        // Start an args variable
        $args = $rawArgs;

        // Remove the first argument (executive)
        unset($args[0]);

        return new ArgumentsModel(array(
            'rawArgs' => array_values($args),
        ));
    }
}
