<?php
declare(strict_types=1);

namespace buzzingpixel\executive\migrations;

use buzzingpixel\executive\abstracts\MigrationAbstract;

/**
 * Class m2018_08_23_172651_AddCoreTemplateRouteHook
 */
class m2018_08_23_172651_AddCoreTemplateRouteHook extends MigrationAbstract
{
    /**
     * Runs the migration
     * @return bool
     */
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

    /**
     * Reverses the migration
     * @return bool
     */
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
