<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\factories\QueryBuilderFactory;
use EllisLab\ExpressionEngine\Library\Filesystem\Filesystem;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;

/**
 * Class MigrationsService
 */
class MigrationsService
{
    /** @var string $table */
    private $table = 'executive_migrations';

    /** @var string $migrationsDir */
    private $migrationsDir = EXECUTIVE_MIGRATION_FILES_PATH;

    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var QueryBuilderFactory $queryBuilderFactory */
    private $queryBuilderFactory;

    /**
     * MigrationsService constructor
     * @param Filesystem $filesystem
     * @param QueryBuilderFactory $queryBuilderFactory
     */
    public function __construct(
        Filesystem $filesystem,
        QueryBuilderFactory $queryBuilderFactory
    ) {
        $this->filesystem = $filesystem;
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * Sets the migrations table
     * @param string $table
     * @return MigrationsService
     */
    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Sets the migrations directory
     * @param string $dir
     * @return MigrationsService
     */
    public function setMigrationsDir(string $dir): self
    {
        $this->migrationsDir = rtrim(rtrim($dir, '/'), DIRECTORY_SEPARATOR);
        return $this;
    }

    /**
     * Gets list of all migrations
     * @return array
     * @throws FilesystemException
     */
    public function getAllMigrations(): array
    {
        $migrationFiles = $this->filesystem->getDirectoryContents(
            $this->migrationsDir
        );

        $allMigrations = [];

        foreach ($migrationFiles as $file) {
            $filePathInfo = pathinfo($file);
            $allMigrations[$filePathInfo['filename']] = $filePathInfo['filename'];
        }

        ksort($allMigrations);

        return $allMigrations;
    }

    /**
     * Gets list of migrations to run
     * @return array
     * @throws FilesystemException
     */
    public function getMigrationsToRun(): array
    {
        $allMigrations = $this->getAllMigrations();

        $tableExists = $this->queryBuilderFactory->make()
            ->table_exists($this->table);

        if (! $tableExists) {
            return $allMigrations;
        }

        /** @var array $runMigrations */
        $runMigrations = $this->queryBuilderFactory->make()
            ->get($this->table)
            ->result();

        foreach ($runMigrations as $record) {
            unset($allMigrations[$record->migration]);
        }

        return $allMigrations;
    }

    /**
     * Adds a run migration to the database
     * @param string $name
     */
    public function addRunMigration(string $name): void
    {
        $this->queryBuilderFactory->make()->insert($this->table, [
            'migration' => $name,
        ]);
    }
}
