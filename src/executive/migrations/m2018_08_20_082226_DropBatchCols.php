<?php

declare(strict_types=1);

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

class m2018_08_20_082226_DropBatchCols extends MigrationAbstract
{
    public function safeUp() : bool
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

    public function safeDown() : bool
    {
        return true;
    }
}
