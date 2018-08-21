<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\models\CommandModel;

/**
 * Class Service
 */
class CommandModelFactory
{
    /**
     * Gets a CommandModel instance
     * @return CommandModel
     */
    public function make(): CommandModel
    {
        return new CommandModel();
    }
}
