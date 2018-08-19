<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use buzzingpixel\executive\factories\QueryBuilderFactory;
use BuzzingPixel\Executive\Controller\MigrationController;

/**
 * Class Executive_upd
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
// @codingStandardsIgnoreStart
class Executive_upd
// @codingStandardsIgnoreEnd
{
    /** @var QueryBuilderFactory $queryBuilderFactory */
    private $queryBuilderFactory;

    /** @var MigrationController $migrationController */
    private $migrationController;

    /**
     * Executive_upd constructor
     * @param QueryBuilderFactory $queryBuilderFactory
     * @param MigrationController $migrationController
     */
    public function __construct(
        QueryBuilderFactory $queryBuilderFactory = null,
        MigrationController $migrationController = null
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory ?:
            new QueryBuilderFactory();

        $this->migrationController = $migrationController ?:
            new MigrationController();
    }

    /**
     * Installs Executive
     * @return bool
     */
    public function install(): bool
    {
        $this->migrationController->runMigrations();
        return true;
    }

    /**
     * Uninstalls Executive
     * @return bool
     */
    public function uninstall(): bool
    {
        $this->migrationController->reverseMigrations();
        return true;
    }

    /**
     * Updates Executive to latest version
     * @return bool
     */
    public function update(): bool
    {
        $this->migrationController->runMigrations();

        $this->queryBuilderFactory->make()->update(
            'modules',
            [
                'module_version' => EXECUTIVE_VER,
            ],
            [
                'module_name' => 'Executive'
            ]
        );

        $this->queryBuilderFactory->make()->update(
            'extensions',
            [
                'version' => EXECUTIVE_VER,
            ],
            [
                'class' => 'Executive_ext'
            ]
        );

        return true;
    }
}
