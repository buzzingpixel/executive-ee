<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\controllers;

use buzzingpixel\executive\services\MigrationsService;
use buzzingpixel\executive\interfaces\MigrationInterface;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;

/**
 * Class RunMigrationsController
 */
class RunMigrationsController
{
    /** @var string $migrationNamespace */
    private $migrationNamespace;

    /** @var MigrationsService $migrationsService */
    private $migrationsService;

    /**
     * RunMigrationsController constructor
     * @param string $migrationNamespace
     * @param MigrationsService $migrationsService
     */
    public function __construct(
        string $migrationNamespace,
        MigrationsService $migrationsService
    ) {
        $this->migrationNamespace = $migrationNamespace;
        $this->migrationsService = $migrationsService;

        $this->migrationsService->setTable('executive_migrations');

        $this->migrationsService->setMigrationsDir(
            EXECUTIVE_MIGRATION_FILES_PATH
        );
    }

    /**
     * Runs up migrations
     * @return bool
     * @throws FilesystemException
     */
    public function migrateUp(): bool
    {
        foreach ($this->migrationsService->getMigrationsToRun() as $name) {
            $className = "{$this->migrationNamespace}\\{$name}";

            if (! class_exists($className)) {
                continue;
            }

            $class = new $className;

            if (! $class instanceof MigrationInterface) {
                continue;
            }

            if (! $class->safeUp()) {
                return false;
            }

            $this->migrationsService->addRunMigration($name);
        }

        return true;
    }

    /**
     * Runs down migrations
     * @return bool
     * @throws FilesystemException
     */
    public function migrateDown(): bool
    {
        $migrations = array_reverse(
            $this->migrationsService->getAllMigrations()
        );

        foreach ($migrations as $name) {
            $className = "{$this->migrationNamespace}\\{$name}";

            if (! class_exists($className)) {
                continue;
            }

            $class = new $className;

            if (! $class instanceof MigrationInterface) {
                continue;
            }

            if (! $class->safeDown()) {
                return false;
            }
        }

        return true;
    }
}
