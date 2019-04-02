<?php

declare(strict_types=1);

namespace BuzzingPixel\Executive\commands;

use buzzingpixel\executive\services\MigrationsService;
use EE_Lang;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class ShowMigrationStatus
{
    /** @var OutputInterface */
    private $consoleOutput;
    /** @var EE_Lang */
    private $lang;
    /** @var MigrationsService */
    private $migrationsService;
    /** @var string */
    private $migrationDestination;

    public function __construct(
        OutputInterface $consoleOutput,
        EE_Lang $lang,
        MigrationsService $migrationsService,
        string $migrationDestination
    ) {
        $this->consoleOutput        = $consoleOutput;
        $this->lang                 = $lang;
        $this->migrationsService    = $migrationsService;
        $this->migrationDestination = $migrationDestination;

        $this->migrationsService->setTable('executive_user_migrations');

        $this->migrationsService->setMigrationsDir(
            $this->migrationDestination
        );
    }

    /**
     * @throws Throwable
     */
    public function showMigrationStatus() : void
    {
        if (! $this->migrationDestination) {
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $this->lang->line('specifyMigrationDestination') .
                '</>'
            );

            return;
        }

        $migrationsStatus = $this->migrationsService->getMigrationsStatus();

        if (! $migrationsStatus) {
            $this->consoleOutput->writeln(
                '<fg=green>' .
                $this->lang->line('noMigrations') .
                '</>'
            );

            return;
        }

        foreach ($migrationsStatus as $status) {
            $this->consoleOutput->write($status['migrationName'] . ': ');

            if ($status['status'] === 'up') {
                $this->consoleOutput->write('<fg=green>up</>', true);

                continue;
            }

            $this->consoleOutput->write('<fg=red>down</>', true);
        }
    }
}
