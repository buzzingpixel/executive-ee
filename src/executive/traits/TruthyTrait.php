<?php

declare(strict_types=1);

namespace buzzingpixel\executive\traits;

use function in_array;
use function is_string;
use function mb_strtolower;

trait TruthyTrait
{
    public static $truthyValues = [
        1,
        '1',
        'y',
        true,
        'yes',
        'true',
    ];

    /**
     * Checks if the incoming value is truthy
     */
    public function isValueTruthy($val) : bool
    {
        if (is_string($val)) {
            $val = mb_strtolower($val);
        }

        return in_array($val, self::$truthyValues, true);
    }
}
