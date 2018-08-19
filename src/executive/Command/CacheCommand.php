<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Command;

use buzzingpixel\executive\abstracts\BaseCommand;

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
