<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/construct/license
 * @link https://buzzingpixel.com/software/construct
 */

/**
 * Class Construct_ext
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
// @codingStandardsIgnoreStart
class Executive_ext
// @codingStandardsIgnoreEnd
{
    /** @var string $version */
    public $version = EXECUTIVE_VER;

    /**
     * core_boot
     * @return mixed
     */
    public function core_boot()
    {
        var_dump('here');
        die;
    }
}
