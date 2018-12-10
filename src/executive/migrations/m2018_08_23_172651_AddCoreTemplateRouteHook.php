<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

class m2018_08_23_172651_AddCoreTemplateRouteHook extends MigrationAbstract
{
    public function safeUp(): bool
    {
        $query = (int) $this->queryBuilderFactory->make()
            ->where('class', 'Executive_ext')
            ->where('method', 'core_template_route')
            ->where('hook', 'core_template_route')
            ->count_all_results('extensions');

        if ($query > 0) {
            return true;
        }

        $this->queryBuilderFactory->make()->insert('extensions', [
            'class' => 'Executive_ext',
            'method' => 'core_template_route',
            'hook' => 'core_template_route',
            'settings' => '',
            'priority' => 1,
            'version' => EXECUTIVE_VER,
            'enabled' => 'y',
        ]);

        return true;
    }

    public function safeDown(): bool
    {
        $this->queryBuilderFactory->make()->delete('extensions', [
            'class' => 'Executive_ext',
            'method' => 'core_template_route',
            'hook' => 'core_template_route',
        ]);

        return true;
    }
}
