<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Migration;

use BuzzingPixel\Executive\Abstracts\BaseMigration;

/**
 * Class m2017_08_27_320040_AddScheduleTrackingTable
 */
class m2017_08_27_320040_AddScheduleTrackingTable extends BaseMigration
{
    /**
     * Run migration
     */
    public function safeUp()
    {
        // Get new instance of query builder
        $queryBuilder = clone $this->queryBuilder;

        // If the table already exists, we don't need to do anything
        if ($queryBuilder->table_exists('executive_schedule_tracking')) {
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
            'name' => array(
                'default' => '',
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
            'isRunning' => array(
                'type' => 'INT',
                'unsigned' => true,
            ),
            'lastRunStartTime' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'lastRunEndTime' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
        ));

        // Set the primary key
        $dbForge->add_key('id', true);

        // Create the table
        $dbForge->create_table('executive_schedule_tracking', true);
    }

    /**
     * Reverse migration
     */
    public function safeDown()
    {
        // Get new instance of query builder
        $queryBuilder = clone $this->queryBuilder;

        // If the table does not exist, we don't need to do anything
        if (! $queryBuilder->table_exists('executive_schedule_tracking')) {
            return;
        }

        // Get new instance of db forge
        $dbForge = clone $this->dbForge;

        // Drop the table
        $dbForge->drop_table('executive_schedule_tracking');
    }
}
