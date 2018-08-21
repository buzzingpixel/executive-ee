<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use Closure;
use ReflectionFunction;
use ReflectionException;

/**
 * Class ReflectionFunctionFactory
 */
class ReflectionFunctionFactory
{
    /**
     * Makes an instance of ReflectionFunction
     * @param Closure $closure
     * @return ReflectionFunction
     * @throws ReflectionException
     */
    public function make(Closure $closure): ReflectionFunction
    {
        return new ReflectionFunction($closure);
    }
}
