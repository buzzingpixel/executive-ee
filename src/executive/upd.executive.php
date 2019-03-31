<?php

declare(strict_types=1);

use buzzingpixel\executive\controllers\RunMigrationsController;
use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use DI\DependencyException;
use DI\NotFoundException;
use EllisLab\ExpressionEngine\Library\Filesystem\FilesystemException;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
class Executive_upd
{
    /** @var QueryBuilderFactory $queryBuilderFactory */
    private $queryBuilderFactory;
    /** @var RunMigrationsController $runMigrationsController */
    private $runMigrationsController;

    /**
     * Executive_upd constructor
     *
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public function __construct(
        ?QueryBuilderFactory $queryBuilderFactory = null,
        ?RunMigrationsController $runMigrationsController = null
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory ?:
            new QueryBuilderFactory();

        $this->runMigrationsController = $runMigrationsController ?:
            ExecutiveDi::diContainer()->get(RunMigrationsController::class);
    }

    /**
     * Installs Executive
     *
     * @throws FilesystemException
     */
    public function install() : bool
    {
        return $this->runMigrationsController->migrateUp();
    }

    /**
     * Uninstalls Executive
     *
     * @throws FilesystemException
     */
    public function uninstall() : bool
    {
        return $this->runMigrationsController->migrateDown();
    }

    /**
     * Updates Executive to latest version
     *
     * @throws FilesystemException
     */
    public function update() : bool
    {
        $status = $this->runMigrationsController->migrateUp();

        if (! $status) {
            return false;
        }

        $this->queryBuilderFactory->make()->update(
            'modules',
            ['module_version' => EXECUTIVE_VER],
            ['module_name' => 'Executive']
        );

        $this->queryBuilderFactory->make()->update(
            'extensions',
            ['version' => EXECUTIVE_VER],
            ['class' => 'Executive_ext']
        );

        return true;
    }
}
