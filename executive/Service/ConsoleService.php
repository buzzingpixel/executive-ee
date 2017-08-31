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
     * @param bool $addBreak
     */
    public function writeLn($line, $color = null, $addBreak = true)
    {
        // Formats
        $reset = $cColor = "\033[0m";
        $red = "\033[31m";
        $green = "\033[32m";
        $yellow = "\033[33m";
        $bold = "\033[1m";

        // Determine if color is something we can deal with
        if ($color === 'red') {
            $cColor = $red;
        } elseif ($color === 'green') {
            $cColor = $green;
        } elseif ($color === 'yellow') {
            $cColor = $yellow;
        }

        $line = strtr($line, array(
            '<red>' => $red,
            '</red>' => $reset,
            '<green>' => $green,
            '</green>' => $reset,
            '<yellow>' => $yellow,
            '</yellow>' => $reset,
            '<bold>' => $bold,
            '</bold>' => $reset,
        ));

        echo "{$cColor}{$line}{$reset}";

        if ($addBreak) {
            echo "\n";
        }
    }
}
