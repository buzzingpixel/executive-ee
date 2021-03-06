<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\services\CliInstallService;
use EE_Lang;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;
use Symfony\Component\Console\Output\OutputInterface;
use function count;

class InstallExecutiveCommand
{
    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;
    /** @var EE_Lang $lang */
    private $lang;
    /** @var array $executiveRawArgs */
    private $executiveRawArgs;
    /** @var QueryBuilderFactory $queryBuilderFactory */
    private $queryBuilderFactory;
    /** @var CliInstallService $cliInstallService */
    private $cliInstallService;

    /**
     * InstallExecutiveCommand constructor
     */
    public function __construct(
        OutputInterface $consoleOutput,
        EE_Lang $lang,
        array $executiveRawArgs,
        QueryBuilderFactory $queryBuilderFactory,
        CliInstallService $cliInstallService
    ) {
        $this->consoleOutput       = $consoleOutput;
        $this->lang                = $lang;
        $this->executiveRawArgs    = $executiveRawArgs;
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->cliInstallService   = $cliInstallService;
    }

    /**
     * Checks if executive is installed. Returns false if installed
     * Returns true if not installed and displays message asking user to install
     * If correct args are in place on CLI, runs install
     *
     * @throws FilesystemException
     */
    public function run() : bool
    {
        $query = (int) $this->queryBuilderFactory->make()
            ->where('module_name', 'Executive')
            ->get('modules')
            ->num_rows();

        if ($query > 0) {
            return false;
        }

        $args = $this->executiveRawArgs;

        $requestingInstall = isset($args[1], $args[2]) &&
            $args[1] === 'executive' &&
            $args[2] === 'install' &&
            count($args) < 4;

        if (! $requestingInstall) {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('notInstalled') .
                '</>'
            );

            return true;
        }

        $this->cliInstallService->run();

        $this->consoleOutput->writeln(
            '<fg=green>' .
            $this->lang->line('executiveInstalled') .
            '</>'
        );

        return true;
    }
}
