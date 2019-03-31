<?php

declare(strict_types=1);

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

class m2017_08_27_320040_AddScheduleTrackingTable extends MigrationAbstract
{
    public function safeUp() : bool
    {
        $tableExists = $this->queryBuilderFactory->make()
            ->table_exists('executive_schedule_tracking');

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
            'name' => [
                'default' => '',
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'isRunning' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'lastRunStartTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'lastRunEndTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $dbForge->add_key('id', true);

        $dbForge->create_table('executive_schedule_tracking', true);

        return true;
    }

    public function safeDown() : bool
    {
        $tableExists = $this->queryBuilderFactory->make()
            ->table_exists('executive_schedule_tracking');

        if (! $tableExists) {
            return true;
        }

        $this->dbForgeFactory->make()
            ->drop_table('executive_schedule_tracking');

        return true;
    }
}
