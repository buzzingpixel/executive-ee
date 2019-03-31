<?php

declare(strict_types=1);

namespace buzzingpixel\executive;

class Noop
{
    public function __invoke() : void
    {
        $this->noop();
    }

    public function noop() : void
    {
    }
}
