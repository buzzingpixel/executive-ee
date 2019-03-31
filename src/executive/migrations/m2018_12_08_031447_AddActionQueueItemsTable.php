<?php

declare(strict_types=1);

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

class m2018_12_08_031447_AddActionQueueItemsTable extends MigrationAbstract
{
    public function safeUp() : bool
    {
        $tableExists = $this->queryBuilderFactory->make()
            ->table_exists('executive_action_queue_items');

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
            'order_to_run' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'action_queue_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'is_finished' => [
                'type' => 'INT',
                'unsigned' => true,
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
            'class' => ['type' => 'TEXT'],
            'method' => ['type' => 'TEXT'],
            'context' => [
                'null' => true,
                'type' => 'TEXT',
            ],
        ]);

        $dbForge->add_key('id', true);

        $dbForge->create_table('executive_action_queue_items', true);

        return true;
    }

    public function safeDown() : bool
    {
        $tableExists = $this->queryBuilderFactory->make()
            ->table_exists('executive_action_queue_items');

        if (! $tableExists) {
            return true;
        }

        $this->dbForgeFactory->make()->drop_table(
            'executive_action_queue_items'
        );

        return true;
    }
}
