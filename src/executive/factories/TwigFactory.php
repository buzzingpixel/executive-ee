<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\ExecutiveDi;
use BuzzingPixel\Executive\services\TwigEnvironment;

class TwigFactory
{
    public function get() : TwigEnvironment
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = ExecutiveDi::diContainer();

        return $di->get(TwigEnvironment::class);
    }
}
