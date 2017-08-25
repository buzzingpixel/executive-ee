<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

use BuzzingPixel\Executive\Controller\MigrationController;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;

/**
 * Class Executive_upd
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
// @codingStandardsIgnoreStart
class Executive_upd
// @codingStandardsIgnoreEnd
{
    /**
     * Install
     * @return bool
     */
    public function install()
    {
        $migrationController = new MigrationController();
        $migrationController->runMigrations();

        // All done
        return true;
    }

    /**
     * Uninstall
     * @return bool
     */
    public function uninstall()
    {
        $migrationController = new MigrationController();
        $migrationController->reverseMigrations();

        // All done
        return true;
    }

    /**
     * Update
     * @param string $current The current version before update
     * @return bool
     */
    public function update($current = '')
    {
        $migrationController = new MigrationController();
        $migrationController->runMigrations();

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = ee('db');

        // Update module version
        $queryBuilder->where('module_name', 'Executive');
        $queryBuilder->update('modules', array(
            'module_version' => EXECUTIVE_VER,
        ));

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = ee('db');

        // Update extension version
        $queryBuilder->where('class', 'Executive_ext');
        $queryBuilder->update('extensions', array(
            'version' => EXECUTIVE_VER,
        ));

        // All done
        return true;
    }
}
