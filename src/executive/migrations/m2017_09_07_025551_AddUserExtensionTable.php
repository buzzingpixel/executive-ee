<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

/**
 * Class m2017_09_07_025551_AddUserExtensionTable
 */
class m2017_09_07_025551_AddUserExtensionTable extends MigrationAbstract
{
    /**
     * Runs the migration
     * @return bool
     */
    public function safeUp(): bool
    {
        $tableExists = $this->queryBuilderFactory->make()
            ->table_exists('executive_user_extensions');

        if ($tableExists) {
            return true;
        }

        $dbForge = $this->dbForgeFactory->make();

        $dbForge->add_field([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'class' => [
                'default' => '',
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'method' => [
                'default' => '',
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'hook' => [
                'default' => '',
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        $dbForge->add_key('id', true);

        $dbForge->create_table('executive_user_extensions', true);

        return true;
    }

    /**
     * Reverses the migration
     * @return bool
     */
    public function safeDown(): bool
    {
        $tableExists = $this->queryBuilderFactory->make()
            ->table_exists('executive_user_extensions');

        if (! $tableExists) {
            return true;
        }

        $this->dbForgeFactory->make()->drop_table('executive_user_extensions');

        return true;
    }
}
