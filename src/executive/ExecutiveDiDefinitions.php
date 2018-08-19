<?php
declare(strict_types=1);

use buzzingpixel\executive\ExecutiveDi;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\ConsoleOutput;
use buzzingpixel\executive\factories\FinderFactory;
use Composer\Repository\InstalledFilesystemRepository;
use buzzingpixel\executive\services\CliInstallService;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\commands\InstallExecutiveCommand;
use buzzingpixel\executive\commands\ComposerProvisionCommand;

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
     * Services
     */
    CliInstallService::class => function () {
        // Manually include non-auto-loaded dependencies
        include_once __DIR__ . '/upd.executive.php';
        return new CliInstallService(new Executive_upd());
    },
];
