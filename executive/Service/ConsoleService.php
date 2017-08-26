<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Service;

use BuzzingPixel\Executive\BaseComponent;

/**
 * Class ConsoleService
 */
class ConsoleService extends BaseComponent
{
    /**
     * Write line
     * @param string $line
     * @param string $color
     */
    public function writeLn($line, $color = null, $addBreak = true)
    {
        // No color
        $reset = $cColor = "\033[0m";

        // Determine if color is something we can deal with
        if ($color === 'red') {
            $cColor = "\033[31m";
        } elseif ($color === 'green') {
            $cColor = "\033[32m";
        } elseif ($color === 'yellow') {
            $cColor = "\033[33m";
        }

        echo "{$cColor}{$line}{$reset}";

        if ($addBreak) {
            echo "\n";
        }
    }
}
