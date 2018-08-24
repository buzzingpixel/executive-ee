<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use buzzingpixel\executive\ExecutiveDi;
use Symfony\Component\Filesystem\Filesystem;
use buzzingpixel\executive\models\RouteModel;
use Symfony\Component\Console\Input\ArgvInput;
use buzzingpixel\executive\services\ViewService;
use buzzingpixel\executive\commands\CacheCommand;
use buzzingpixel\executive\factories\EeDiFactory;
use buzzingpixel\executive\commands\ConfigCommand;
use buzzingpixel\executive\services\RoutingService;
use Symfony\Component\Console\Output\ConsoleOutput;
use buzzingpixel\executive\factories\FinderFactory;
use buzzingpixel\executive\services\ScheduleService;
use buzzingpixel\executive\services\CommandsService;
use buzzingpixel\executive\models\CliArgumentsModel;
use buzzingpixel\executive\services\MigrationsService;
use buzzingpixel\executive\services\RunCommandService;
use Composer\Repository\InstalledFilesystemRepository;
use buzzingpixel\executive\services\CliInstallService;
use buzzingpixel\executive\commands\RunScheduleCommand;
use buzzingpixel\executive\services\CliQuestionService;
use buzzingpixel\executive\factories\SplFileInfoFactory;
use buzzingpixel\executive\commands\AddOnUpdatesCommand;
use buzzingpixel\executive\services\TemplateMakerService;
use buzzingpixel\executive\factories\CommandModelFactory;
use buzzingpixel\executive\commands\MakeMigrationCommand;
use buzzingpixel\executive\controllers\ConsoleController;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\services\LayoutDesignerService;
use buzzingpixel\executive\services\CaseConversionService;
use buzzingpixel\executive\services\ElevateSessionService;
use buzzingpixel\executive\services\CliErrorHandlerService;
use buzzingpixel\executive\services\ChannelDesignerService;
use buzzingpixel\executive\commands\MakeFromTemplateCommand;
use buzzingpixel\executive\commands\InstallExecutiveCommand;
use buzzingpixel\executive\factories\ConsoleQuestionFactory;
use buzzingpixel\executive\commands\RunUserMigrationsCommand;
use buzzingpixel\executive\services\ExtensionDesignerService;
use buzzingpixel\executive\commands\ComposerProvisionCommand;
use buzzingpixel\executive\commands\ListUserMigrationsCommand;
use buzzingpixel\executive\factories\ScheduleItemModelFactory;
use buzzingpixel\executive\factories\CliArgumentsModelFactory;
use buzzingpixel\executive\controllers\RunMigrationsController;
use buzzingpixel\executive\factories\ReflectionFunctionFactory;
use buzzingpixel\executive\factories\ClosureFromCallableFactory;

return [
    /**
     * Commands
     */
    AddOnUpdatesCommand::class => function () {
        return new AddOnUpdatesCommand(
            ee('Addon'),
            new ConsoleOutput(),
            ExecutiveDi::get(CliQuestionService::class),
            ee()->lang
        );
    },
    CacheCommand::class => function () {
        return new CacheCommand(
            ee()->functions,
            new ConsoleOutput(),
            ee()->lang
        );
    },
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
    ConfigCommand::class => function () {
        return new ConfigCommand(
            ee()->config,
            ee()->lang,
            ExecutiveDi::get(CliQuestionService::class)
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
    ListUserMigrationsCommand::class => function () {
        /** @var \EE_Config $config */
        $config = ee()->config;
        $nameSpace = $config->item('migrationNamespace');
        $destination = $config->item('migrationDestination');

        return new ListUserMigrationsCommand(
            new ConsoleOutput(),
            ee()->lang,
            ExecutiveDi::get(MigrationsService::class),
            \is_string($nameSpace) ? $nameSpace : '',
            \is_string($destination) ? $destination : ''
        );
    },
    MakeFromTemplateCommand::class => function () {
        /** @var \EE_Config $config */
        $config = ee()->config;

        $availableConfigurations = $config->item('classTemplateConfigurations');

        return new MakeFromTemplateCommand(
            new ConsoleOutput(),
            ExecutiveDi::get(CliQuestionService::class),
            ee()->lang,
            ExecutiveDi::get(CaseConversionService::class),
            ExecutiveDi::get(TemplateMakerService::class),
            \is_array($availableConfigurations) ? $availableConfigurations : []
        );
    },
    MakeMigrationCommand::class => function () {
        /** @var \EE_Config $config */
        $config = ee()->config;
        $templateLocation = $config->item('migrationTemplateLocation');
        $nameSpace = $config->item('migrationNamespace');
        $destination = $config->item('migrationDestination');

        return new MakeMigrationCommand(
            new ConsoleOutput(),
            ExecutiveDi::get(CliQuestionService::class),
            ee()->lang,
            ExecutiveDi::get(CaseConversionService::class),
            ExecutiveDi::get(TemplateMakerService::class),
            \is_string($templateLocation) ? $templateLocation : '',
            \is_string($nameSpace) ? $nameSpace : '',
            \is_string($destination) ? $destination : ''
        );
    },
    RunScheduleCommand::class => function () {
        // Let's try not to run out of time
        @set_time_limit(0);

        return new RunScheduleCommand(
            ee()->lang,
            new ConsoleOutput(),
            ExecutiveDi::get(ScheduleService::class),
            ExecutiveDi::get(RunCommandService::class)
        );
    },
    RunUserMigrationsCommand::class => function () {
        /** @var \EE_Config $config */
        $config = ee()->config;
        $nameSpace = $config->item('migrationNamespace');
        $destination = $config->item('migrationDestination');

        return new RunUserMigrationsCommand(
            new ConsoleOutput(),
            ee()->lang,
            ExecutiveDi::get(MigrationsService::class),
            \is_string($nameSpace) ? $nameSpace : '',
            \is_string($destination) ? $destination : '',
            new ExecutiveDi()
        );
    },

    /**
     * Controllers
     */
    ConsoleController::class => function () {
        return new ConsoleController(
            ExecutiveDi::get(CliArgumentsModel::class),
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
     * Models
     */
    CliArgumentsModel::class => function () {
        $arguments = EXECUTIVE_RAW_ARGS;
        $arguments = \is_array($arguments) ? $arguments : [];
        return new CliArgumentsModel($arguments);
    },
    RouteModel::SINGLETON_DI_NAME => function () {
        /** @var EE_Session $session */
        $session = ee()->session;

        $class = $session->cache('Executive', RouteModel::SINGLETON_DI_NAME);

        if ($class) {
            return $class;
        }

        $class = new RouteModel();

        $session->set_cache('Executive', RouteModel::SINGLETON_DI_NAME, $class);

        return $class;
    },

    /**
     * Services
     */
    CaseConversionService::class => function () {
        return new CaseConversionService();
    },
    ChannelDesignerService::class => function () {
        return new ChannelDesignerService(ee('Model'));
    },
    CliInstallService::class => function () {
        return new CliInstallService(new Executive_upd());
    },
    CliErrorHandlerService::class => function () {
        return new CliErrorHandlerService(
            new ConsoleOutput(),
            ee()->lang
        );
    },
    CliQuestionService::class => function () {
        return new CliQuestionService(
            (new Symfony\Component\Console\Application())
                ->getHelperSet()
                ->get('question'),
            new ArgvInput(),
            new ConsoleOutput(),
            new ConsoleQuestionFactory(),
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
    ExtensionDesignerService::class => function () {
        return new ExtensionDesignerService(
            ee('Model'),
            ee('db')
        );
    },
    LayoutDesignerService::class => function () {
        return new LayoutDesignerService(ee('Model'));
    },
    MigrationsService::class => function () {
        return new MigrationsService(
            ee('Filesystem'),
            new QueryBuilderFactory()
        );
    },
    RoutingService::class => function () {
        /** @var \EE_Config $config */
        $config = ee()->config;
        $routes = $config->item('customRoutes');

        /** @var \EE_Loader $loader */
        $loader = ee()->load;
        $loader->library('template', null, 'TMPL');

        return new RoutingService(
            ExecutiveDi::get(RouteModel::SINGLETON_DI_NAME),
            \is_array($routes) ? $routes : [],
            ee()->lang,
            new ExecutiveDi(),
            ee()->TMPL,
            ee()->config
        );
    },
    RunCommandService::class => function () {
        return new RunCommandService(
            ExecutiveDi::get(CliArgumentsModel::class),
            new ExecutiveDi(),
            new EeDiFactory(),
            new ClosureFromCallableFactory(),
            new ReflectionFunctionFactory()
        );
    },
    ScheduleService::class => function () {
        return new ScheduleService(
            ee()->config,
            ee('Addon'),
            ExecutiveDi::get(CommandsService::class),
            new ScheduleItemModelFactory(),
            new QueryBuilderFactory(),
            new CliArgumentsModelFactory()
        );
    },
    TemplateMakerService::class => function () {
        return new TemplateMakerService(
            new SplFileInfoFactory(),
            new Filesystem()
        );
    },
    ViewService::class => function () {
        /** @var \EE_Config $config */
        $config = ee()->config;
        $viewsBasePath = $config->item('cpViewsBasePath');

        return new ViewService(
            ee('executive:Provider'),
            \is_string($viewsBasePath) ? $viewsBasePath : ''
        );
    },
    ViewService::INTERNAL_DI_NAME => function () {
        return new ViewService(
            ee('executive:Provider'),
            __DIR__ . '/views'
        );
    },
];
