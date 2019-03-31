<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use buzzingpixel\executive\exceptions\InvalidMigrationException;
use buzzingpixel\executive\interfaces\MigrationInterface;
use buzzingpixel\executive\services\MigrationsService;
use EE_Lang;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function array_map;
use function str_replace;

class RunUserMigrationsCommand
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
    /** @var ContainerInterface $di */
    private $di;

    /**
     * RunUserMigrationsCommand constructor
     */
    public function __construct(
        OutputInterface $consoleOutput,
        EE_Lang $lang,
        MigrationsService $migrationsService,
        string $migrationNamespace,
        string $makeMigrationDestination,
        ContainerInterface $di
    ) {
        $this->consoleOutput        = $consoleOutput;
        $this->lang                 = $lang;
        $this->migrationsService    = $migrationsService;
        $this->migrationNamespace   = $migrationNamespace;
        $this->migrationDestination = $makeMigrationDestination;
        $this->di                   = $di;

        $this->migrationsService->setTable('executive_user_migrations');

        $this->migrationsService->setMigrationsDir(
            $this->migrationDestination
        );
    }

    /**
     * Runs user migrations
     *
     * @throws FilesystemException
     */
    public function runMigrations() : void
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

        array_map([$this, 'runMigration'], $migrationsToRun);

        $this->consoleOutput->writeln(
            '<fg=green>' .
            $this->lang->line('migrationsFinished') .
            '</>'
        );
    }

    /**
     * Runs a migration
     *
     * @throws InvalidMigrationException
     */
    public function runMigration(string $migrationClassName) : void
    {
        $className = "{$this->migrationNamespace}\\{$migrationClassName}";

        try {
            $class = $this->di->get($className);
        } catch (Throwable $e) {
            $class = new $className();
        }

        if (! $class instanceof MigrationInterface) {
            throw new InvalidMigrationException(
                str_replace(
                    '{{class}}',
                    $migrationClassName,
                    $this->lang->line('migrationDoesNotImplementInterface')
                )
            );
        }

        $this->consoleOutput->writeln(
            '<fg=yellow>' .
            str_replace(
                '{{class}}',
                $migrationClassName,
                $this->lang->line('runningMigration')
            ) .
            '...</>'
        );

        if (! $class->safeUp()) {
            throw new InvalidMigrationException(
                str_replace(
                    '{{class}}',
                    $migrationClassName,
                    $this->lang->line('migrationReportedFalse')
                )
            );
        }

        $this->migrationsService->addRunMigration($migrationClassName);

        $this->consoleOutput->writeln(
            '<fg=green>' .
            $this->lang->line('migrationRanSuccessfully') .
            '</>'
        );
    }
}
