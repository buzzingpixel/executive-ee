<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Migration;

use BuzzingPixel\Executive\Abstracts\BaseMigration;

/**
 * Class m2017_08_25_171531_AddCoreBootExtensionHook
 */
class m2017_08_25_171531_AddCoreBootExtensionHook extends BaseMigration
{
    /**
     * Run migration
     */
    public function safeUp()
    {
        // Check if the extension is already installed
        $query = (int) $this->queryBuilder->where('class', 'Executive_ext')
            ->where('method', 'core_boot')
            ->where('hook', 'core_boot')
            ->count_all_results('extensions');

        // If there is a result, we can end processing
        if ($query > 0) {
            return;
        }

        // Insert extension record
        $this->queryBuilder->insert('extensions', array(
            'class' => 'Executive_ext',
            'method' => 'core_boot',
            'hook' => 'core_boot',
            'priority' => 1,
            'version' => EXECUTIVE_VER,
            'enabled' => 'y',
        ));
    }

    /**
     * Reverse migration
     */
    public function safeDown()
    {
        // Delete extension record
        $this->queryBuilder->where('class', 'Executive_ext');
        $this->queryBuilder->where('method', 'core_boot');
        $this->queryBuilder->where('hook', 'core_boot');
        $this->queryBuilder->delete('extensions');
    }
}
