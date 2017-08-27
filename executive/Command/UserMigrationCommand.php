<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Command;

use BuzzingPixel\Executive\Abstracts\BaseCommand;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;
use BuzzingPixel\Executive\Abstracts\BaseMigration;

/**
 * Class UserMigrationController
 */
class UserMigrationCommand extends BaseCommand
{
    /** @var \CI_DB_mysqli_forge $dbForge */
    private $dbForge;

    /** @var QueryBuilder $queryBuilder */
    private $queryBuilder;

    /** @var array $runMigrationsList */
    private $runMigrationsList = array();

    /** @var int $batch */
    private $batch = 1;

    /** @var array $migrationClasses */
    private $migrationClasses = array();

    /** @var string $migrationFilesPath */
    private $migrationFilesPath;

    /**
     * MigrationService initialization
     */
    public function initCommand()
    {
        ee()->load->dbforge();
        $this->dbForge = ee()->dbforge;
        $this->queryBuilder = ee('db');
        $path = realpath(SYSPATH);
        $this->migrationFilesPath = "{$path}/user/Migration";
    }

    /**
     * Run migrations
     */
    public function runMigrations()
    {
        // Build migration list
        $this->getRunMigrationsList();

        // Start an array for migrations to add to the database
        $migrationsRun = array();

        // Iterate through migration classes
        foreach ($this->getMigrationClasses() as $shortClassName => $className) {
            // Check if this migration has been run
            if (isset($this->runMigrationsList[$shortClassName])) {
                continue;
            }

            /** @var BaseMigration $class */
            $class = new $className(
                clone $this->dbForge,
                clone $this->queryBuilder
            );

            // Make sure class is an instance of BaseMigration
            if (! $class instanceof BaseMigration) {
                continue;
            }

            // Run the migration
            $class->safeUp();

            // Add this migration to the migrations run for database insertion
            $migrationsRun[] = array(
                'migration' => $shortClassName,
                'batch' => $this->batch,
            );
        }

        // Get new instance of query builder
        $queryBuilder = clone $this->queryBuilder;

        // Add the run migrations to the database
        if ($migrationsRun) {
            $queryBuilder->insert_batch('executive_user_migrations', $migrationsRun);
        }

        // If no migrations were run, tell the user
        if (! $migrationsRun) {
            $this->consoleService->writeLn(lang('noMigrationsToRun'), 'green');
            return;
        }

        $this->consoleService->writeLn(lang('followingMigrationsRun'));

        foreach ($migrationsRun as $migration) {
            $this->consoleService->writeLn(
                '  ' . $migration['migration'],
                'green'
            );
        }
    }

    /**
     * Get migration list
     */
    private function getRunMigrationsList()
    {
        // Get new instance of query builder
        $queryBuilder = clone $this->queryBuilder;

        // Get the list
        /** @var array $list */
        $list = $queryBuilder->get('executive_user_migrations')->result();

        // Determine next batch number and set list keys
        $returnList = array();
        $batchNum = 0;
        foreach ($list as $item) {
            $batchNum = (int) max($batchNum, $item->batch);
            $returnList[$item->migration] = $item;
        }
        $batchNum++;

        // Set the migration list
        $this->runMigrationsList = $returnList;

        // Set this batch number
        $this->batch = $batchNum;
    }

    /**
     * Get migration classes
     * @return array
     */
    private function getMigrationClasses()
    {
        // Start an array
        $list = array();

        // Iterate through files and add them to the list
        foreach (new \DirectoryIterator($this->migrationFilesPath) as $file) {
            // Make sure this is actually a file
            if ($file->isDot() || $file->getExtension() !== 'php') {
                continue;
            }

            // Get the class name
            $className = $file->getBasename('.php');
            $classNameFull = "\User\Migration\\{$className}";

            // Add the class name to the list
            $list[$className] = $classNameFull;
        }

        // Make sure the list is sorted correctly
        ksort($list);

        // Set the list
        $this->migrationClasses = $list;

        // Return the list
        return $list;
    }
}
