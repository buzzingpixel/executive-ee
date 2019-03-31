<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use Symfony\Component\Finder\Finder;

class FinderFactory
{
    /**
     * Makes an instance of the Finder
     */
    public function make() : Finder
    {
        return new Finder();
    }
}
