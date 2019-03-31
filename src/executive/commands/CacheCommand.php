<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use EE_Functions;
use EE_Lang;
use Symfony\Component\Console\Output\OutputInterface;

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
     */
    public function __construct(
        EE_Functions $functions,
        OutputInterface $consoleOutput,
        EE_Lang $lang
    ) {
        $this->functions     = $functions;
        $this->consoleOutput = $consoleOutput;
        $this->lang          = $lang;
    }

    /**
     * Clear caches
     */
    public function clearCaches(string $type) : void
    {
        $this->functions->clear_caching($type ?: 'all');

        $this->consoleOutput->writeln(
            '<fg=green>' . $this->lang->line('cachesCleared') . '</>'
        );
    }
}
