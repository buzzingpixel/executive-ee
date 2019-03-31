<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

class EeDiFactory
{
    /**
     * Gets a CommandModel instance
     *
     * @return mixed
     */
    public function make(string $which)
    {
        return ee($which);
    }
}
