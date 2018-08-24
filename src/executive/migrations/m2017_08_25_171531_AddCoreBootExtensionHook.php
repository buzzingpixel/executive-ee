<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

/**
 * Class m2017_08_25_171531_AddCoreBootExtensionHook
 */
class m2017_08_25_171531_AddCoreBootExtensionHook extends MigrationAbstract
{
    /**
     * Runs the migration
     * @return bool
     */
    public function safeUp(): bool
    {
        $query = (int) $this->queryBuilderFactory->make()
            ->where('class', 'Executive_ext')
            ->where('method', 'core_boot')
            ->where('hook', 'core_boot')
            ->count_all_results('extensions');

        if ($query > 0) {
            return true;
        }

        $this->queryBuilderFactory->make()->insert('extensions', [
            'class' => 'Executive_ext',
            'method' => 'core_boot',
            'hook' => 'core_boot',
            'settings' => '',
            'priority' => 1,
            'version' => EXECUTIVE_VER,
            'enabled' => 'y',
        ]);

        return true;
    }

    /**
     * Reverses the migration
     * @return bool
     */
    public function safeDown(): bool
    {
        $this->queryBuilderFactory->make()->delete('extensions', [
            'class' => 'Executive_ext',
            'method' => 'core_boot',
            'hook' => 'core_boot',
        ]);

        return true;
    }
}
