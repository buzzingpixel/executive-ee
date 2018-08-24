<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\traits;

/**
 * Class TruthyTrait
 */
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
     * @param mixed $val
     * @return bool
     */
    public function isValueTruthy($val): bool
    {
        if (\is_string($val)) {
            $val = strtolower($val);
        }

        return \in_array($val, self::$truthyValues, true);
    }
}
