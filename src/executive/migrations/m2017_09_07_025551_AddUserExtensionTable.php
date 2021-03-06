<?php

declare(strict_types=1);

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

class m2017_09_07_025551_AddUserExtensionTable extends MigrationAbstract
{
    public function safeUp() : bool
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

    public function safeDown() : bool
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
