<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Migration;

use BuzzingPixel\Executive\Abstracts\BaseMigration;

/**
 * Class m2017_08_25_171356_AddModule
 */
class m2017_08_25_171356_AddModule extends BaseMigration
{
    /**
     * Run migration
     */
    public function safeUp()
    {
        // Check if the module is already installed
        $query = (int) $this->queryBuilder->where('module_name', 'Executive')
            ->count_all_results('modules');

        // If there is a result, we can end processing
        if ($query > 0) {
            return;
        }

        // Insert module record
        $this->queryBuilder->insert('modules', array(
            'module_name' => 'Executive',
            'module_version' => EXECUTIVE_VER,
            'has_cp_backend' => 'n',
            'has_publish_fields' => 'n',
        ));
    }

    /**
     * Reverse migration
     */
    public function safeDown()
    {
        // Delete row from modules
        $this->queryBuilder->where('module_name', 'Executive');
        $this->queryBuilder->delete('modules');
    }
}
