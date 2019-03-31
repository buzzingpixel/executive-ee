<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\models\CommandModel;

class CommandModelFactory
{
    /**
     * Gets a CommandModel instance
     */
    public function make() : CommandModel
    {
        return new CommandModel();
    }
}
