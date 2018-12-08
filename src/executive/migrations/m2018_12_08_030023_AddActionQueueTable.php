<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

class m2018_12_08_030023_AddActionQueueTable extends MigrationAbstract
{
    public function safeUp(): bool
    {
        $tableExists = $this->queryBuilderFactory->make()
            ->table_exists('executive_action_queue');

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
            'action_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'action_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'has_started' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'is_finished' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'finished_due_to_error' => [
                'default' => 0,
                'type' => 'INT',
                'unsigned' => true,
            ],
            'percent_complete' => [
                'default' => 0,
                'type' => 'FLOAT',
                'unsigned' => true,
            ],
            'added_at' => [
                'type' => 'DATETIME',
            ],
            'added_at_time_zone' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'finished_at' => [
                'null' => true,
                'type' => 'DATETIME',
            ],
            'finished_at_time_zone' => [
                'null' => true,
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'context' => [
                'null' => true,
                'type' => 'TEXT',
            ],
        ]);

        $dbForge->add_key('id', true);

        $dbForge->create_table('executive_action_queue', true);

        return true;
    }

    public function safeDown(): bool
    {
        $tableExists = $this->queryBuilderFactory->make()
            ->table_exists('executive_action_queue');

        if (! $tableExists) {
            return true;
        }

        $this->dbForgeFactory->make()->drop_table('executive_action_queue');

        return true;
    }
}
