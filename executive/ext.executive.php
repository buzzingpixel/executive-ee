<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use BuzzingPixel\Executive\Controller\ConsoleController;

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
    // @codingStandardsIgnoreStart
    public function sessions_start() // @codingStandardsIgnoreEnd
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
    // @codingStandardsIgnoreStart
    public function core_boot() // @codingStandardsIgnoreEnd
    {
        // Check for console request
        if (! defined('REQ') || REQ !== 'CONSOLE') {
            return;
        }

        // Get the console controller
        /** @var ConsoleController $consoleController */
        $consoleController = ee('executive:ConsoleController');

        // Run the console controller
        $consoleController->runConsoleRequest();

        // Make sure we exit here
        exit;
    }
}
