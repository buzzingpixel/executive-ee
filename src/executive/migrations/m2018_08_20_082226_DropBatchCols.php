<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

/**
 * Class m2018_08_20_082226_DropBatchCols
 */
class m2018_08_20_082226_DropBatchCols extends MigrationAbstract
{
    /**
     * Runs the migration
     * @return bool
     */
    public function safeUp(): bool
    {
        $execColExists = $this->queryBuilderFactory->make()->field_exists(
            'batch',
            'executive_migrations'
        );

        if ($execColExists) {
            $dbForge = $this->dbForgeFactory->make();
            $dbForge->drop_column('executive_migrations', 'batch');
        }

        $userColExists = $this->queryBuilderFactory->make()->field_exists(
            'batch',
            'executive_user_migrations'
        );

        if ($userColExists) {
            $dbForge = $this->dbForgeFactory->make();
            $dbForge->drop_column('executive_user_migrations', 'batch');
        }

        return true;
    }

    /**
     * Reverses the migration
     * @return bool
     */
    public function safeDown(): bool
    {
        return true;
    }
}
