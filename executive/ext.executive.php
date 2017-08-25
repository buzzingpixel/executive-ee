<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

/**
 * Class Executive_ext
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
     * session_start
     */
    public function sessions_start()
    {
        // Check for console request
        if (! defined('REQ') || REQ !== 'CONSOLE') {
            return;
        }

        /** @var \EE_Config $configService */
        $configService = ee()->config;
        $configService->set_item('disable_csrf_protection', 'y');
    }

    /**
     * core_boot
     */
    public function core_boot()
    {
        // Check for console request
        if (! defined('REQ') || REQ !== 'CONSOLE') {
            return;
        }

        // TODO: do stuff with console requests
        var_dump('here');
        die;
    }
}
