<?php
declare(strict_types=1);

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\ConsoleOutput;
use buzzingpixel\executive\factories\FinderFactory;
use Composer\Repository\InstalledFilesystemRepository;
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
];
