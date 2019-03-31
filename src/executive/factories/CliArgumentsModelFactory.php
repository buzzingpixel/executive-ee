<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\models\CliArgumentsModel;

class CliArgumentsModelFactory
{
    /**
     * Gets a CliArgumentsModel instance
     */
    public function make(array $rawArguments = []) : CliArgumentsModel
    {
        return new CliArgumentsModel($rawArguments);
    }
}
