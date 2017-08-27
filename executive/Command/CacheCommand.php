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
 * Class CacheCommand
 */
class CacheCommand extends BaseCommand
{
    /**
     * Clear caches
     * @param string $type
     */
    public function clearCaches($type)
    {
        ee()->functions->clear_caching($type ?: 'all');

        $this->consoleService->writeLn(lang('cachesCleared'), 'green');
    }
}
