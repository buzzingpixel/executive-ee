<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Migration;

use BuzzingPixel\Executive\Abstracts\BaseMigration;

/**
 * Class m2017_08_26_235904_AddUserMigrationsTable
 */
class m2017_08_26_235904_AddUserMigrationsTable extends BaseMigration
{
    /**
     * Run migration
     */
    public function safeUp()
    {
        // Get new instance of query builder
        $queryBuilder = clone $this->queryBuilder;

        // If the table already exists, we don't need to do anything
        if ($queryBuilder->table_exists('executive_user_migrations')) {
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
        $dbForge->create_table('executive_user_migrations', true);
    }

    /**
     * Reverse migration
     */
    public function safeDown()
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
}
