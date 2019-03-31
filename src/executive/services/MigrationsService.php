<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\factories\QueryBuilderFactory;
use EllisLab\ExpressionEngine\Library\Filesystem\Filesystem;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;
use const DIRECTORY_SEPARATOR;
use function ksort;
use function pathinfo;
use function rtrim;

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
     */
    public function __construct(
        Filesystem $filesystem,
        QueryBuilderFactory $queryBuilderFactory
    ) {
        $this->filesystem          = $filesystem;
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * Sets the migrations table
     *
     * @return MigrationsService
     */
    public function setTable(string $table) : self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Sets the migrations directory
     *
     * @return MigrationsService
     */
    public function setMigrationsDir(string $dir) : self
    {
        $this->migrationsDir = rtrim(rtrim($dir, '/'), DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * Gets list of all migrations
     *
     * @throws FilesystemException
     */
    public function getAllMigrations() : array
    {
        $migrationFiles = $this->filesystem->getDirectoryContents(
            $this->migrationsDir
        );

        $allMigrations = [];

        foreach ($migrationFiles as $file) {
            $filePathInfo                             = pathinfo($file);
            $allMigrations[$filePathInfo['filename']] = $filePathInfo['filename'];
        }

        ksort($allMigrations);

        return $allMigrations;
    }

    /**
     * Gets list of migrations to run
     *
     * @throws FilesystemException
     */
    public function getMigrationsToRun() : array
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
     */
    public function addRunMigration(string $name) : void
    {
        $this->queryBuilderFactory->make()->insert($this->table, ['migration' => $name]);
    }
}
