<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\models\CliArgumentsModel;

/**
 * Class CliArgumentsModelFactory
 */
class CliArgumentsModelFactory
{
    /**
     * Gets a CliArgumentsModel instance
     * @param array $rawArguments
     * @return CliArgumentsModel
     */
    public function make(array $rawArguments = []): CliArgumentsModel
    {
        return new CliArgumentsModel($rawArguments);
    }
}
