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
 * Class m2017_08_25_171356_AddModule
 */
class m2017_08_25_171356_AddModule extends MigrationAbstract
{
    /**
     * Runs the migration
     * @return bool
     */
    public function safeUp(): bool
    {
        $query = (int) $this->queryBuilderFactory->make()
            ->where('module_name', 'Executive')
            ->count_all_results('modules');

        if ($query > 0) {
            return true;
        }

        $this->queryBuilderFactory->make()->insert('modules', [
            'module_name' => 'Executive',
            'module_version' => EXECUTIVE_VER,
            'has_cp_backend' => 'n',
            'has_publish_fields' => 'n',
        ]);

        return true;
    }

    /**
     * Reverses the migration
     * @return bool
     */
    public function safeDown(): bool
    {
        $this->queryBuilderFactory->make()->delete('modules', [
            'module_name' => 'Executive',
        ]);

        return true;
    }
}
