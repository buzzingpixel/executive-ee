<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive;

class Noop
{
    public function __invoke()
    {
        $this->noop();
    }

    public function noop(): void
    {
    }
}
