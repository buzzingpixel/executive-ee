<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use Closure;

/**
 * Class ClosureFromCallableFactory
 */
class ClosureFromCallableFactory
{
    /**
     * Makes an instance of ReflectionFunction
     * @param callable $callable
     * @return Closure
     */
    public function make(callable $callable): Closure
    {
        return Closure::fromCallable($callable);
    }
}
