<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use Closure;
use ReflectionException;
use ReflectionFunction;

class ReflectionFunctionFactory
{
    /**
     * Makes an instance of ReflectionFunction
     *
     * @throws ReflectionException
     */
    public function make(Closure $closure) : ReflectionFunction
    {
        return new ReflectionFunction($closure);
    }
}
