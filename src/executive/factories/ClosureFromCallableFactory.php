<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use Closure;

class ClosureFromCallableFactory
{
    /**
     * Makes an instance of ReflectionFunction
     */
    public function make(callable $callable) : Closure
    {
        return Closure::fromCallable($callable);
    }
}
