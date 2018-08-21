<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\commands;

use EE_Lang;
use EE_Functions;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CacheCommand
 */
class CacheCommand
{
    /** @var EE_Functions $functions */
    private $functions;

    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;

    /** @var EE_Lang $lang */
    private $lang;

    /**
     * CacheCommand constructor
     * @param EE_Functions $functions
     * @param OutputInterface $consoleOutput
     * @param EE_Lang $lang
     */
    public function __construct(
        EE_Functions $functions,
        OutputInterface $consoleOutput,
        EE_Lang $lang
    ) {
        $this->functions = $functions;
        $this->consoleOutput = $consoleOutput;
        $this->lang = $lang;
    }

    /**
     * Clear caches
     * @param string $type
     */
    public function clearCaches($type): void
    {
        $this->functions->clear_caching($type ?: 'all');

        $this->consoleOutput->writeln(
            '<fg=green>' . $this->lang->line('cachesCleared') . '</>'
        );
    }
}
