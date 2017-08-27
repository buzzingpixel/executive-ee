<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Command;

use BuzzingPixel\Executive\Abstracts\BaseCommand;

/**
 * Class Service
 */
class ConfigCommand extends BaseCommand
{
    /** @var \EE_Config $eeConfigService */
    private $eeConfigService;

    /**
     * Initialize
     */
    public function initCommand()
    {
        $this->eeConfigService = ee()->config;
    }

    /**
     * Get config item
     * @param string $key
     * @param string $index
     */
    public function get($key, $index)
    {
        if ($key === null) {
            $this->consoleService->writeLn(lang('keyMustBeSpecified'), 'red');
            return;
        }

        $val = $this->eeConfigService->item($key, $index);

        print_r('(' . gettype($val) . ') ');
        print_r($val);
        $this->consoleService->writeLn('');
    }
}
