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
use buzzingpixel\executive\services\QueueApi;
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
use buzzingpixel\executive\commands\RunQueueCommand;
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
use buzzingpixel\executive\commands\SyncTemplatesCommand;
use buzzingpixel\executive\services\LayoutDesignerService;
use buzzingpixel\executive\services\CaseConversionService;
use buzzingpixel\executive\services\ElevateSessionService;
use buzzingpixel\executive\services\CliErrorHandlerService;
use buzzingpixel\executive\services\ChannelDesignerService;
use buzzingpixel\executive\services\queue\AddToQueueService;
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
use buzzingpixel\executive\services\queue\GetNextQueueItemService;
use buzzingpixel\executive\services\queue\MarkQueueItemAsRunService;
use buzzingpixel\executive\services\queue\MarkAsStoppedDueToErrorService;
use buzzingpixel\executive\services\queue\UpdateActionQueueStatusService;
use buzzingpixel\executive\services\templatesync\SyncTemplatesFromFilesService;
use buzzingpixel\executive\services\templatesync\DeleteSnippetsNotOnDiskService;
use buzzingpixel\executive\services\templatesync\DeleteTemplatesNotOnDiskService;
use buzzingpixel\executive\services\templatesync\DeleteVariablesNotOnDiskService;
use buzzingpixel\executive\services\templatesync\EnsureIndexTemplatesExistService;
use buzzingpixel\executive\services\templatesync\ForceSnippetVarSyncToDatabaseService;
use buzzingpixel\executive\services\templatesync\DeleteTemplateGroupsWithoutTemplatesService;

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
        // Edge case and weirdness with composer
        getenv('HOME') || putenv('HOME=' . APP_DIR);

        $composerApp = new Composer\Console\Application();
        /** @noinspection PhpUnhandledExceptionInspection */
        $composer = $composerApp->getComposer();
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
    RunQueueCommand::class => function () {
        // Let's try not to run out of time
        @set_time_limit(0);

        return new RunQueueCommand(
            new ExecutiveDi(),
            ExecutiveDi::get(QueueApi::class)
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
    SyncTemplatesCommand::class => function () {
        return new SyncTemplatesCommand(
            ee()->lang,
            new ConsoleOutput(),
            ee()->config,
            ExecutiveDi::get(DeleteVariablesNotOnDiskService::class),
            ExecutiveDi::get(EnsureIndexTemplatesExistService::class),
            ExecutiveDi::get(DeleteSnippetsNotOnDiskService::class),
            ExecutiveDi::get(DeleteTemplatesNotOnDiskService::class),
            ExecutiveDi::get(ForceSnippetVarSyncToDatabaseService::class),
            ExecutiveDi::get(SyncTemplatesFromFilesService::class),
            ExecutiveDi::get(DeleteTemplateGroupsWithoutTemplatesService::class)
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
        // TODO: So php-di caches classes by default with the get() method
        // TODO: and you have to use make() to get new instances so
        // TODO: this all may be unnecessary. But needs testing.
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
    AddToQueueService::class => function () {
        return new AddToQueueService(new QueryBuilderFactory());
    },
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
    DeleteSnippetsNotOnDiskService::class => function () {
        return new DeleteSnippetsNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            ExecutiveDi::get('eeSiteShortNames'),
            ee('Model'),
            new Filesystem()
        );
    },
    DeleteTemplateGroupsWithoutTemplatesService::class => function () {
        return new DeleteTemplateGroupsWithoutTemplatesService(ee('Model'));
    },
    DeleteTemplatesNotOnDiskService::class => function () {
        return new DeleteTemplatesNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            ExecutiveDi::get('eeSiteShortNames'),
            ee('Model'),
            new Filesystem()
        );
    },
    DeleteVariablesNotOnDiskService::class => function () {
        return new DeleteVariablesNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            ExecutiveDi::get('eeSiteShortNames'),
            ee('Model'),
            new Filesystem()
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
    EnsureIndexTemplatesExistService::class => function () {
        return new EnsureIndexTemplatesExistService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            new FinderFactory(),
            new Filesystem()
        );
    },
    ExtensionDesignerService::class => function () {
        return new ExtensionDesignerService(ee('Model'), ee('db'));
    },
    ForceSnippetVarSyncToDatabaseService::class => function () {
        return new ForceSnippetVarSyncToDatabaseService(ee('Model'));
    },
    GetNextQueueItemService::class => function () {
        return new GetNextQueueItemService(new QueryBuilderFactory());
    },
    LayoutDesignerService::class => function () {
        return new LayoutDesignerService(ee('Model'));
    },
    MarkAsStoppedDueToErrorService::class => function () {
        return new MarkAsStoppedDueToErrorService(new QueryBuilderFactory());
    },
    MarkQueueItemAsRunService::class => function () {
        return new MarkQueueItemAsRunService(
            new QueryBuilderFactory(),
            ExecutiveDi::get(UpdateActionQueueStatusService::class)
        );
    },
    MigrationsService::class => function () {
        return new MigrationsService(
            ee('Filesystem'),
            new QueryBuilderFactory()
        );
    },
    QueueApi::class => function () {
        return new QueueApi(new ExecutiveDi());
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
    SyncTemplatesFromFilesService::class => function () {
        return new SyncTemplatesFromFilesService();
    },
    TemplateMakerService::class => function () {
        return new TemplateMakerService(
            new SplFileInfoFactory(),
            new Filesystem()
        );
    },
    UpdateActionQueueStatusService::class => function () {
        return new UpdateActionQueueStatusService(new QueryBuilderFactory());
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

    /**
     * Other
     */
    'eeSiteShortNames' => function () {
        $sites = ee('Model')->get('Site')
            ->fields('site_name')
            ->all();

        $sitesArray = [];

        foreach ($sites as $site) {
            $sitesArray[$site->site_id] = $site->site_name;
        }

        return $sitesArray;
    },
];
