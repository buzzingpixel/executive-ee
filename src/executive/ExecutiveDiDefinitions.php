<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use buzzingpixel\executive\ExecutiveDi;
use Symfony\Component\Filesystem\Filesystem;
use buzzingpixel\executive\factories\EeDiFactory;
use Symfony\Component\Console\Output\ConsoleOutput;
use buzzingpixel\executive\factories\FinderFactory;
use buzzingpixel\executive\services\CommandsService;
use buzzingpixel\executive\services\MigrationsService;
use buzzingpixel\executive\services\RunCommandService;
use Composer\Repository\InstalledFilesystemRepository;
use buzzingpixel\executive\services\CliInstallService;
use buzzingpixel\executive\factories\CommandModelFactory;
use buzzingpixel\executive\controllers\ConsoleController;
use \buzzingpixel\executive\services\CliArgumentsService;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\services\ElevateSessionService;
use buzzingpixel\executive\services\CliErrorHandlerService;
use buzzingpixel\executive\commands\InstallExecutiveCommand;
use buzzingpixel\executive\commands\ComposerProvisionCommand;
use buzzingpixel\executive\controllers\RunMigrationsController;
use buzzingpixel\executive\factories\ReflectionFunctionFactory;
use buzzingpixel\executive\factories\ClosureFromCallableFactory;

return [
    /**
     * Commands
     */
    ComposerProvisionCommand::class => function () {
        $composerApp = new Composer\Console\Application();
        /** @noinspection PhpUnhandledExceptionInspection */
        $composer = $composerApp->getComposer();
        $repositoryManager = $composer->getRepositoryManager();
        /** @var InstalledFilesystemRepository $installedFilesystemRepository */
        $installedFilesystemRepository = $repositoryManager->getLocalRepository();

        $package = $composer->getPackage();

        return new ComposerProvisionCommand(
            new ConsoleOutput(),
            $installedFilesystemRepository,
            rtrim($composer->getConfig()->get('vendor-dir'), DIRECTORY_SEPARATOR),
            new Filesystem(),
            new FinderFactory(),
            $package->getExtra()['publicDir'] ?? 'public',
            $package->getExtra()['eeAddOns'] ?? [],
            $package->getExtra()['installFromDownload'] ?? []
        );
    },
    InstallExecutiveCommand::class => function () {
        return new InstallExecutiveCommand(
            new ConsoleOutput(),
            ee()->lang,
            defined('EXECUTIVE_RAW_ARGS') && \is_array(EXECUTIVE_RAW_ARGS) ?
                EXECUTIVE_RAW_ARGS :
                [],
            new QueryBuilderFactory(),
            ExecutiveDi::get(CliInstallService::class)
        );
    },

    /**
     * Controllers
     */
    ConsoleController::class => function () {
        return new ConsoleController(
            ExecutiveDi::get(CliArgumentsService::class),
            new ConsoleOutput(),
            ExecutiveDi::get(CommandsService::class),
            ee()->lang,
            ExecutiveDi::get(RunCommandService::class)
        );
    },
    RunMigrationsController::class => function () {
        return new RunMigrationsController(
            '\buzzingpixel\executive\migrations',
            ExecutiveDi::get(MigrationsService::class)
        );
    },

    /**
     * Services
     */
    CliArgumentsService::class => function () {
        $arguments = EXECUTIVE_RAW_ARGS;
        $arguments = \is_array($arguments) ? $arguments : [];
        return new CliArgumentsService($arguments);
    },
    CliInstallService::class => function () {
        // Manually include non-auto-loaded dependencies
        include_once __DIR__ . '/upd.executive.php';
        return new CliInstallService(new Executive_upd());
    },
    CliErrorHandlerService::class => function () {
        return new CliErrorHandlerService(
            new ConsoleOutput(),
            ee()->lang
        );
    },
    CommandsService::class => function () {
        return new CommandsService(
            ee()->config,
            ee('Addon'),
            new CommandModelFactory()
        );
    },
    ElevateSessionService::class => function () {
        return new ElevateSessionService(
            new QueryBuilderFactory(),
            ee()->session,
            ee()->router,
            ee()->load
        );
    },
    MigrationsService::class => function () {
        return new MigrationsService(
            ee('Filesystem'),
            new QueryBuilderFactory()
        );
    },
    RunCommandService::class => function () {
        return new RunCommandService(
            ExecutiveDi::get(CliArgumentsService::class),
            new ExecutiveDi(),
            new EeDiFactory(),
            new ClosureFromCallableFactory(),
            new ReflectionFunctionFactory()
        );
    },
];
