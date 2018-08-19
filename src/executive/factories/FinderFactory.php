<?php
declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use Symfony\Component\Finder\Finder;

/**
 * Class FinderFactory
 */
class FinderFactory
{
    /**
     * Makes an instance of the Finder
     * @return Finder
     */
    public function make(): Finder
    {
        return new Finder();
    }
}
