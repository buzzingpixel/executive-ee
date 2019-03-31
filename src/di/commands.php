<?php

declare(strict_types=1);

use buzzingpixel\executive\commands\AddOnUpdatesCommand;
use buzzingpixel\executive\commands\CacheCommand;
use buzzingpixel\executive\commands\ComposerProvisionCommand;
use buzzingpixel\executive\commands\ConfigCommand;
use buzzingpixel\executive\commands\InstallExecutiveCommand;
use buzzingpixel\executive\commands\ListUserMigrationsCommand;
use buzzingpixel\executive\commands\MakeFromTemplateCommand;
use buzzingpixel\executive\commands\MakeMigrationCommand;
use buzzingpixel\executive\commands\RunQueueCommand;
use buzzingpixel\executive\commands\RunScheduleCommand;
use buzzingpixel\executive\commands\RunUserMigrationsCommand;
use buzzingpixel\executive\commands\SyncTemplatesCommand;
use buzzingpixel\executive\factories\FinderFactory;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\services\CaseConversionService;
use buzzingpixel\executive\services\CliInstallService;
use buzzingpixel\executive\services\CliQuestionService;
use buzzingpixel\executive\services\MigrationsService;
use buzzingpixel\executive\services\QueueApi;
use buzzingpixel\executive\services\RunCommandService;
use buzzingpixel\executive\services\ScheduleService;
use buzzingpixel\executive\services\TemplateMakerService;
use buzzingpixel\executive\services\templatesync\DeleteSnippetsNotOnDiskService;
use buzzingpixel\executive\services\templatesync\DeleteTemplateGroupsWithoutTemplatesService;
use buzzingpixel\executive\services\templatesync\DeleteTemplatesNotOnDiskService;
use buzzingpixel\executive\services\templatesync\DeleteVariablesNotOnDiskService;
use buzzingpixel\executive\services\templatesync\EnsureIndexTemplatesExistService;
use buzzingpixel\executive\services\templatesync\ForceSnippetVarSyncToDatabaseService;
use buzzingpixel\executive\services\templatesync\SyncTemplatesFromFilesService;
use Composer\Repository\InstalledFilesystemRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;

return [
    AddOnUpdatesCommand::class => static function (ContainerInterface $di) {
        return new AddOnUpdatesCommand(
            ee('Addon'),
            new ConsoleOutput(),
            $di->get(CliQuestionService::class),
            ee()->lang
        );
    },
    CacheCommand::class => static function () {
        return new CacheCommand(
            ee()->functions,
            new ConsoleOutput(),
            ee()->lang
        );
    },
    ComposerProvisionCommand::class => static function () {
        // Edge case and weirdness with composer
        getenv('HOME') || putenv('HOME=' . APP_DIR);

        $composerApp = new Composer\Console\Application();
        /** @noinspection PhpUnhandledExceptionInspection */
        $composer          = $composerApp->getComposer();
        $repositoryManager = $composer->getRepositoryManager();
        /** @var InstalledFilesystemRepository $installedFilesystemRepository */
        $installedFilesystemRepository = $repositoryManager->getLocalRepository();

        $package = $composer->getPackage();

        $extra = $package->getExtra();

        $provisionEEVersionTag = (string) ($extra['provisionEEVersionTag'] ?? '');

        return new ComposerProvisionCommand(
            new ConsoleOutput(),
            $installedFilesystemRepository,
            rtrim($composer->getConfig()->get('vendor-dir'), DIRECTORY_SEPARATOR),
            new Filesystem(),
            new FinderFactory(),
            $extra['publicDir'] ?? 'public',
            $extra['eeAddOns'] ?? [],
            $extra['installFromDownload'] ?? [],
            $provisionEEVersionTag
        );
    },
    ConfigCommand::class => static function (ContainerInterface $di) {
        return new ConfigCommand(
            ee()->config,
            ee()->lang,
            $di->get(CliQuestionService::class)
        );
    },
    InstallExecutiveCommand::class => static function (ContainerInterface $di) {
        return new InstallExecutiveCommand(
            new ConsoleOutput(),
            ee()->lang,
            defined('EXECUTIVE_RAW_ARGS') && is_array(EXECUTIVE_RAW_ARGS) ?
                EXECUTIVE_RAW_ARGS :
                [],
            new QueryBuilderFactory(),
            $di->get(CliInstallService::class)
        );
    },
    ListUserMigrationsCommand::class => static function (ContainerInterface $di) {
        /** @var EE_Config $config */
        $config      = ee()->config;
        $nameSpace   = $config->item('migrationNamespace');
        $destination = $config->item('migrationDestination');

        return new ListUserMigrationsCommand(
            new ConsoleOutput(),
            ee()->lang,
            $di->get(MigrationsService::class),
            is_string($nameSpace) ? $nameSpace : '',
            is_string($destination) ? $destination : ''
        );
    },
    MakeFromTemplateCommand::class => static function (ContainerInterface $di) {
        /** @var EE_Config $config */
        $config = ee()->config;

        $availableConfigurations = $config->item('classTemplateConfigurations');

        return new MakeFromTemplateCommand(
            new ConsoleOutput(),
            $di->get(CliQuestionService::class),
            ee()->lang,
            $di->get(CaseConversionService::class),
            $di->get(TemplateMakerService::class),
            is_array($availableConfigurations) ?
                $availableConfigurations :
                []
        );
    },
    MakeMigrationCommand::class => static function (ContainerInterface $di) {
        /** @var EE_Config $config */
        $config           = ee()->config;
        $templateLocation = $config->item('migrationTemplateLocation');
        $nameSpace        = $config->item('migrationNamespace');
        $destination      = $config->item('migrationDestination');

        return new MakeMigrationCommand(
            new ConsoleOutput(),
            $di->get(CliQuestionService::class),
            ee()->lang,
            $di->get(CaseConversionService::class),
            $di->get(TemplateMakerService::class),
            is_string($templateLocation) ? $templateLocation : '',
            is_string($nameSpace) ? $nameSpace : '',
            is_string($destination) ? $destination : ''
        );
    },
    RunQueueCommand::class => static function (ContainerInterface $di) {
        // Let's try not to run out of time
        @set_time_limit(0);

        return new RunQueueCommand(
            $di,
            $di->get(QueueApi::class)
        );
    },
    RunScheduleCommand::class => static function (ContainerInterface $di) {
        // Let's try not to run out of time
        @set_time_limit(0);

        return new RunScheduleCommand(
            ee()->lang,
            new ConsoleOutput(),
            $di->get(ScheduleService::class),
            $di->get(RunCommandService::class)
        );
    },
    RunUserMigrationsCommand::class => static function (ContainerInterface $di) {
        /** @var EE_Config $config */
        $config      = ee()->config;
        $nameSpace   = $config->item('migrationNamespace');
        $destination = $config->item('migrationDestination');

        return new RunUserMigrationsCommand(
            new ConsoleOutput(),
            ee()->lang,
            $di->get(MigrationsService::class),
            is_string($nameSpace) ? $nameSpace : '',
            is_string($destination) ? $destination : '',
            $di
        );
    },
    SyncTemplatesCommand::class => static function (ContainerInterface $di) {
        return new SyncTemplatesCommand(
            ee()->lang,
            new ConsoleOutput(),
            ee()->config,
            $di->get(DeleteVariablesNotOnDiskService::class),
            $di->get(EnsureIndexTemplatesExistService::class),
            $di->get(DeleteSnippetsNotOnDiskService::class),
            $di->get(DeleteTemplatesNotOnDiskService::class),
            $di->get(ForceSnippetVarSyncToDatabaseService::class),
            $di->get(SyncTemplatesFromFilesService::class),
            $di->get(DeleteTemplateGroupsWithoutTemplatesService::class)
        );
    },
];