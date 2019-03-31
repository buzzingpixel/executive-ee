<?php

declare(strict_types=1);

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

class m2017_08_25_174225_AddSessionsStartHook extends MigrationAbstract
{
    public function safeUp() : bool
    {
        $query = (int) $this->queryBuilderFactory->make()
            ->where('class', 'Executive_ext')
            ->where('method', 'sessions_start')
            ->where('hook', 'sessions_start')
            ->count_all_results('extensions');

        if ($query > 0) {
            return true;
        }

        $this->queryBuilderFactory->make()->insert('extensions', [
            'class' => 'Executive_ext',
            'method' => 'sessions_start',
            'hook' => 'sessions_start',
            'settings' => '',
            'priority' => 1,
            'version' => EXECUTIVE_VER,
            'enabled' => 'y',
        ]);

        return true;
    }

    public function safeDown() : bool
    {
        $this->queryBuilderFactory->make()->delete('extensions', [
            'class' => 'Executive_ext',
            'method' => 'sessions_start',
            'hook' => 'sessions_start',
        ]);

        return true;
    }
}
