<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

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
