<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use DI\NotFoundException;
use DI\DependencyException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\controllers\RunMigrationsController;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;
use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;

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

    /** @var RunMigrationsController $runMigrationsController */
    private $runMigrationsController;

    /**
     * Executive_upd constructor
     * @param QueryBuilderFactory $queryBuilderFactory
     * @param RunMigrationsController $runMigrationsController
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public function __construct(
        QueryBuilderFactory $queryBuilderFactory = null,
        RunMigrationsController $runMigrationsController = null
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory ?:
            new QueryBuilderFactory();

        $this->runMigrationsController = $runMigrationsController ?:
            ExecutiveDi::make(RunMigrationsController::class);
    }

    /**
     * Installs Executive
     * @return bool
     * @throws FilesystemException
     */
    public function install(): bool
    {
        return $this->runMigrationsController->migrateUp();
    }

    /**
     * Uninstalls Executive
     * @return bool
     * @throws FilesystemException
     */
    public function uninstall(): bool
    {
        return $this->runMigrationsController->migrateDown();
    }

    /**
     * Updates Executive to latest version
     * @return bool
     * @throws FilesystemException
     */
    public function update(): bool
    {
        $status = $this->runMigrationsController->migrateUp();

        if (! $status) {
            return false;
        }

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
