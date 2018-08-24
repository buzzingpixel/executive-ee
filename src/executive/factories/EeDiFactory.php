<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

/**
 * Class Service
 */
class EeDiFactory
{
    /**
     * Gets a CommandModel instance
     * @param string $which
     * @return mixed
     */
    public function make(string $which)
    {
        return ee($which);
    }
}
