<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use buzzingpixel\executive\exceptions\InvalidMigrationException;
use buzzingpixel\executive\interfaces\MigrationInterface;
use buzzingpixel\executive\services\CliQuestionService;
use buzzingpixel\executive\services\MigrationsService;
use EE_Lang;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function str_replace;

class ReverseUserMigrationsCommand
{
    /** @var OutputInterface */
    private $consoleOutput;
    /** @var EE_Lang */
    private $lang;
    /** @var MigrationsService */
    private $migrationsService;
    /** @var string */
    private $migrationNamespace;
    /** @var string */
    private $migrationDestination;
    /** @var ContainerInterface */
    private $di;
    /** @var CliQuestionService */
    private $questionService;

    public function __construct(
        OutputInterface $consoleOutput,
        EE_Lang $lang,
        MigrationsService $migrationsService,
        string $migrationNamespace,
        string $migrationDestination,
        ContainerInterface $di,
        CliQuestionService $questionService
    ) {
        $this->consoleOutput        = $consoleOutput;
        $this->lang                 = $lang;
        $this->migrationsService    = $migrationsService;
        $this->migrationNamespace   = $migrationNamespace;
        $this->migrationDestination = $migrationDestination;
        $this->di                   = $di;
        $this->questionService      = $questionService;

        $this->migrationsService->setTable('executive_user_migrations');

        $this->migrationsService->setMigrationsDir(
            $this->migrationDestination
        );
    }

    /**
     * Reverses user migrations
     *
     * @throws InvalidMigrationException
     */
    public function reverseMigrations() : void
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

        $migrationsToReverse = $this->migrationsService->getRunMigrations();

        if (! $migrationsToReverse) {
            $this->consoleOutput->writeln(
                '<fg=green>' .
                $this->lang->line('noMigrationsToReverse') .
                '</>'
            );

            return;
        }

        $target = $this->questionService->ask(
            '<fg=cyan>' .
            $this->lang->line('specifyMigrationTarget') .
            ': </>',
            false
        );

        if ($target && ! isset($migrationsToReverse[$target])) {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('invalidReverseMigrationTarget') .
                '</>'
            );

            return;
        }

        $hasLooped = false;

        foreach ($migrationsToReverse as $migration) {
            if ($hasLooped && ! $target && $target !== '0') {
                $this->consoleOutput->writeln(
                    '<fg=green>' .
                    $this->lang->line('successfullyReversedLastMigration') .
                    '</>'
                );

                break;
            }

            if ($target && $migration === $target) {
                $this->consoleOutput->writeln(
                    '<fg=green>' .
                    $this->lang->line('successfullyReverseMigratedTo:') .
                    ' ' . $target .
                    '</>'
                );

                break;
            }

            $this->reverseMigration($migration);

            $hasLooped = true;
        }

        $this->consoleOutput->writeln(
            '<fg=green>' .
            $this->lang->line('migrationsReverseFinished') .
            '</>'
        );
    }

    /**
     * Reverses a user migration
     *
     * @throws InvalidMigrationException
     */
    public function reverseMigration(string $migrationClassName) : void
    {
        $className = $this->migrationNamespace . '\\' . $migrationClassName;

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
                $this->lang->line('reversingMigration')
            ) .
            '...</>'
        );

        if (! $class->safeDown()) {
            throw new InvalidMigrationException(
                str_replace(
                    '{{class}}',
                    $migrationClassName,
                    $this->lang->line('migrationReportedFalse')
                )
            );
        }

        $this->migrationsService->removeRunMigration($migrationClassName);

        $this->consoleOutput->writeln(
            '<fg=green>' .
            $this->lang->line('migrationReversedSuccessfully') .
            '</>'
        );
    }
}
