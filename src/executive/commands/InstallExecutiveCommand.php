<?php
declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use EE_Lang;
use Symfony\Component\Console\Output\OutputInterface;
use buzzingpixel\executive\services\CliInstallService;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;

/**
 * Class InstallExecutiveCommand
 */
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
     * @param OutputInterface $consoleOutput
     * @param EE_Lang $lang
     * @param array $executiveRawArgs
     * @param QueryBuilderFactory $queryBuilderFactory
     * @param CliInstallService $cliInstallService
     */
    public function __construct(
        OutputInterface $consoleOutput,
        EE_Lang $lang,
        array $executiveRawArgs,
        QueryBuilderFactory $queryBuilderFactory,
        CliInstallService $cliInstallService
    ) {
        $this->consoleOutput = $consoleOutput;
        $this->lang = $lang;
        $this->executiveRawArgs = $executiveRawArgs;
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->cliInstallService = $cliInstallService;
    }

    /**
     * Checks if executive is installed. Returns false if installed
     * Returns true if not installed and displays message asking user to install
     * If correct args are in place on CLI, runs install
     * @return bool
     * @throws FilesystemException
     */
    public function run(): bool
    {
        $query = (int) $this->queryBuilderFactory->make()
            ->where('module_name', 'Executive')
            ->get('modules')
            ->num_rows();

        if ($query > 0) {
            return false;
        }

        $args = $this->executiveRawArgs;

        $requestingInstall = isset($args[1]) &&
            $args[1] === 'install' &&
            \count($args) < 3;

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
