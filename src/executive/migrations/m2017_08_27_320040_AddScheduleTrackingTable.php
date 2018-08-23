<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

/**
 * Class m2017_08_27_320040_AddScheduleTrackingTable
 */
class m2017_08_27_320040_AddScheduleTrackingTable extends MigrationAbstract
{
    /**
     * Runs the migration
     * @return bool
     */
    public function safeUp(): bool
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
                'default' => '0000-00-00 00:00:00',
            ],
            'lastRunEndTime' => [
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ],
        ]);

        $dbForge->add_key('id', true);

        $dbForge->create_table('executive_schedule_tracking', true);

        return true;
    }

    /**
     * Reverses the migration
     * @return bool
     */
    public function safeDown(): bool
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
