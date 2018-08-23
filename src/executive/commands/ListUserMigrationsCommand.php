<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\commands;

use EE_Lang;
use Symfony\Component\Console\Output\OutputInterface;
use buzzingpixel\executive\services\MigrationsService;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;

/**
 * Class ListUserMigrationsCommand
 */
class ListUserMigrationsCommand
{
    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;

    /** @var EE_Lang $lang */
    private $lang;

    /** @var MigrationsService $migrationsService */
    private $migrationsService;

    /** @var string $migrationNamespace */
    private $migrationNamespace;

    /** @var string $migrationDestination */
    private $migrationDestination;

    /**
     * ListUserMigrationsCommand constructor
     * @param OutputInterface $consoleOutput
     * @param EE_Lang $lang
     * @param MigrationsService $migrationsService
     * @param string $migrationNamespace
     * @param string $makeMigrationDestination
     */
    public function __construct(
        OutputInterface $consoleOutput,
        EE_Lang $lang,
        MigrationsService $migrationsService,
        string $migrationNamespace,
        string $makeMigrationDestination
    ) {
        $this->consoleOutput = $consoleOutput;
        $this->lang = $lang;
        $this->migrationsService = $migrationsService;
        $this->migrationNamespace = $migrationNamespace;
        $this->migrationDestination = $makeMigrationDestination;

        $this->migrationsService->setTable('executive_user_migrations');

        $this->migrationsService->setMigrationsDir(
            $this->migrationDestination
        );
    }

    /**
     * Runs user migrations
     * @throws FilesystemException
     */
    public function run(): void
    {
        $hasBlockingErrors = false;

        if (! $this->migrationNamespace) {
            $hasBlockingErrors = true;

            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('specifyMigrationNamespace') .
                '</>'
            );
        }

        if (! $this->migrationDestination) {
            $hasBlockingErrors = true;

            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('specifyMigrationDestination') .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $migrationsToRun = $this->migrationsService->getMigrationsToRun();

        if (! $migrationsToRun) {
            $this->consoleOutput->writeln(
                '<fg=green>' .
                $this->lang->line('noMigrationsToRun') .
                '</>'
            );

            return;
        }

        if (count($migrationsToRun) === 1) {
            $this->consoleOutput->writeln(
                '<fg=yellow>' .
                $this->lang->line('followingMigrationHasntRun') .
                '</>'
            );

            array_map([$this, 'listMigration'], $migrationsToRun);

            return;
        }

        $this->consoleOutput->writeln(
            '<fg=yellow>' .
            $this->lang->line('followingMigrationsHaventRun') .
            '</>'
        );

        array_map([$this, 'listMigration'], $migrationsToRun);
    }

    /**
     * Lists migration
     * @param string $migrationClassName
     */
    public function listMigration(string $migrationClassName): void
    {
        $this->consoleOutput->writeln(
            '<fg=green>  ' . $migrationClassName . '</>'
        );
    }
}
