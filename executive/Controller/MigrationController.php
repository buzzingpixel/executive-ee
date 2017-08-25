<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Controller;

use BuzzingPixel\Executive\BaseComponent;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;
use BuzzingPixel\Executive\Abstracts\BaseMigration;

/**
 * Class MigrationController
 */
class MigrationController extends BaseComponent
{
    /** @var \CI_DB_mysqli_forge $dbForge */
    private $dbForge;

    /** @var QueryBuilder $queryBuilder */
    private $queryBuilder;

    /** @var string $migrationFilesPath */
    private $migrationFilesPath;

    /** @var int $batch */
    private $batch = 1;

    /** @var array $runMigrationsList */
    private $runMigrationsList = array();

    /** @var array $migrationClasses */
    private $migrationClasses = array();

    /**
     * MigrationService initialization
     */
    public function init()
    {
        $this->dbForge = ee()->dbforge;
        $this->queryBuilder = ee('db');
        $this->migrationFilesPath = EXECUTIVE_MIGRATION_FILES_PATH;
    }

    /**
     * Run migrations
     */
    public function runMigrations()
    {
        // Make sure Construct Migrations table exists
        $this->installMigrationsTable();

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
            $queryBuilder->insert_batch('executive_migrations', $migrationsRun);
        }
    }

    /**
     * Reverse migrations
     */
    public function reverseMigrations()
    {
        // Iterate through migrations and take them down
        foreach (array_reverse($this->getMigrationClasses()) as $className) {
            /** @var BaseMigration $class */
            $class = new $className(
                clone $this->dbForge,
                clone $this->queryBuilder
            );

            // Make sure class is an instance of BaseMigration
            if (! $class instanceof BaseMigration) {
                continue;
            }

            // Take the migration down
            $class->safeDown();
        }

        // Remove migrations table
        $this->removeMigrationsTable();
    }

    /**
     * Install migrations table
     */
    private function installMigrationsTable()
    {
        // Get new instance of query builder
        $queryBuilder = clone $this->queryBuilder;

        // If the table already exists, we don't need to do anything
        if ($queryBuilder->table_exists('executive_migrations')) {
            return;
        }

        // Get new instance of db forge
        $dbForge = clone $this->dbForge;

        // Add fields
        $dbForge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'migration' => array(
                'default' => '',
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
            'batch' => array(
                'type' => 'INT',
                'unsigned' => true,
            ),
        ));

        // Set the primary key
        $dbForge->add_key('id', true);

        // Create the table
        $dbForge->create_table('executive_migrations', true);
    }

    /**
     * Remove migrations table
     */
    private function removeMigrationsTable()
    {
        // Get new instance of query builder
        $queryBuilder = clone $this->queryBuilder;

        // If the table does not exist, we don't need to do anything
        if (! $queryBuilder->table_exists('executive_migrations')) {
            return;
        }

        // Get new instance of db forge
        $dbForge = clone $this->dbForge;

        // Drop the table
        $dbForge->drop_table('executive_migrations');
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
        $list = $queryBuilder->get('executive_migrations')->result();

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
            $classNameFull = "\BuzzingPixel\Construct\Migration\\{$className}";

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
