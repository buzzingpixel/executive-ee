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
use buzzingpixel\executive\controllers\ConsoleController;
use buzzingpixel\executive\controllers\RunMigrationsController;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\factories\CliArgumentsModelFactory;
use buzzingpixel\executive\factories\ClosureFromCallableFactory;
use buzzingpixel\executive\factories\CommandModelFactory;
use buzzingpixel\executive\factories\ConsoleQuestionFactory;
use buzzingpixel\executive\factories\EeDiFactory;
use buzzingpixel\executive\factories\FinderFactory;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\factories\ReflectionFunctionFactory;
use buzzingpixel\executive\factories\ScheduleItemModelFactory;
use buzzingpixel\executive\factories\SplFileInfoFactory;
use buzzingpixel\executive\factories\TwigFactory;
use buzzingpixel\executive\models\CliArgumentsModel;
use buzzingpixel\executive\models\RouteModel;
use buzzingpixel\executive\services\CaseConversionService;
use buzzingpixel\executive\services\ChannelDesignerService;
use buzzingpixel\executive\services\CliErrorHandlerService;
use buzzingpixel\executive\services\CliInstallService;
use buzzingpixel\executive\services\CliQuestionService;
use buzzingpixel\executive\services\CommandsService;
use buzzingpixel\executive\services\EETemplateService;
use buzzingpixel\executive\services\ElevateSessionService;
use buzzingpixel\executive\services\ExtensionDesignerService;
use buzzingpixel\executive\services\LayoutDesignerService;
use buzzingpixel\executive\services\MigrationsService;
use buzzingpixel\executive\services\queue\AddToQueueService;
use buzzingpixel\executive\services\queue\GetNextQueueItemService;
use buzzingpixel\executive\services\queue\MarkAsStoppedDueToErrorService;
use buzzingpixel\executive\services\queue\MarkQueueItemAsRunService;
use buzzingpixel\executive\services\queue\UpdateActionQueueStatusService;
use buzzingpixel\executive\services\QueueApi;
use buzzingpixel\executive\services\RoutingService;
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
use buzzingpixel\executive\services\ViewService;
use buzzingpixel\executive\twigextensions\EETemplateTwigExtension;
use Composer\Repository\InstalledFilesystemRepository;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment as TwigEnvironment;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

return [
    /**
     * Commands
     */
    AddOnUpdatesCommand::class => static function () {
        return new AddOnUpdatesCommand(
            ee('Addon'),
            new ConsoleOutput(),
            ExecutiveDi::get(CliQuestionService::class),
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
    ConfigCommand::class => static function () {
        return new ConfigCommand(
            ee()->config,
            ee()->lang,
            ExecutiveDi::get(CliQuestionService::class)
        );
    },
    InstallExecutiveCommand::class => static function () {
        return new InstallExecutiveCommand(
            new ConsoleOutput(),
            ee()->lang,
            defined('EXECUTIVE_RAW_ARGS') && is_array(EXECUTIVE_RAW_ARGS) ?
                EXECUTIVE_RAW_ARGS :
                [],
            new QueryBuilderFactory(),
            ExecutiveDi::get(CliInstallService::class)
        );
    },
    ListUserMigrationsCommand::class => static function () {
        /** @var EE_Config $config */
        $config      = ee()->config;
        $nameSpace   = $config->item('migrationNamespace');
        $destination = $config->item('migrationDestination');

        return new ListUserMigrationsCommand(
            new ConsoleOutput(),
            ee()->lang,
            ExecutiveDi::get(MigrationsService::class),
            is_string($nameSpace) ? $nameSpace : '',
            is_string($destination) ? $destination : ''
        );
    },
    MakeFromTemplateCommand::class => static function () {
        /** @var EE_Config $config */
        $config = ee()->config;

        $availableConfigurations = $config->item('classTemplateConfigurations');

        return new MakeFromTemplateCommand(
            new ConsoleOutput(),
            ExecutiveDi::get(CliQuestionService::class),
            ee()->lang,
            ExecutiveDi::get(CaseConversionService::class),
            ExecutiveDi::get(TemplateMakerService::class),
            is_array($availableConfigurations) ? $availableConfigurations : []
        );
    },
    MakeMigrationCommand::class => static function () {
        /** @var EE_Config $config */
        $config           = ee()->config;
        $templateLocation = $config->item('migrationTemplateLocation');
        $nameSpace        = $config->item('migrationNamespace');
        $destination      = $config->item('migrationDestination');

        return new MakeMigrationCommand(
            new ConsoleOutput(),
            ExecutiveDi::get(CliQuestionService::class),
            ee()->lang,
            ExecutiveDi::get(CaseConversionService::class),
            ExecutiveDi::get(TemplateMakerService::class),
            is_string($templateLocation) ? $templateLocation : '',
            is_string($nameSpace) ? $nameSpace : '',
            is_string($destination) ? $destination : ''
        );
    },
    RunQueueCommand::class => static function () {
        // Let's try not to run out of time
        @set_time_limit(0);

        return new RunQueueCommand(
            new ExecutiveDi(),
            ExecutiveDi::get(QueueApi::class)
        );
    },
    RunScheduleCommand::class => static function () {
        // Let's try not to run out of time
        @set_time_limit(0);

        return new RunScheduleCommand(
            ee()->lang,
            new ConsoleOutput(),
            ExecutiveDi::get(ScheduleService::class),
            ExecutiveDi::get(RunCommandService::class)
        );
    },
    RunUserMigrationsCommand::class => static function () {
        /** @var EE_Config $config */
        $config      = ee()->config;
        $nameSpace   = $config->item('migrationNamespace');
        $destination = $config->item('migrationDestination');

        return new RunUserMigrationsCommand(
            new ConsoleOutput(),
            ee()->lang,
            ExecutiveDi::get(MigrationsService::class),
            is_string($nameSpace) ? $nameSpace : '',
            is_string($destination) ? $destination : '',
            new ExecutiveDi()
        );
    },
    SyncTemplatesCommand::class => static function () {
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
    ConsoleController::class => static function () {
        return new ConsoleController(
            ExecutiveDi::get(CliArgumentsModel::class),
            new ConsoleOutput(),
            ExecutiveDi::get(CommandsService::class),
            ee()->lang,
            ExecutiveDi::get(RunCommandService::class)
        );
    },
    RunMigrationsController::class => static function () {
        return new RunMigrationsController(
            '\buzzingpixel\executive\migrations',
            ExecutiveDi::get(MigrationsService::class)
        );
    },

    /**
     * Models
     */
    CliArgumentsModel::class => static function () {
        $arguments = EXECUTIVE_RAW_ARGS;
        $arguments = is_array($arguments) ? $arguments : [];

        return new CliArgumentsModel($arguments);
    },
    RouteModel::SINGLETON_DI_NAME => static function () {
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
    AddToQueueService::class => static function () {
        return new AddToQueueService(new QueryBuilderFactory());
    },
    CaseConversionService::class => static function () {
        return new CaseConversionService();
    },
    ChannelDesignerService::class => static function () {
        return new ChannelDesignerService(ee('Model'));
    },
    CliInstallService::class => static function () {
        return new CliInstallService(new Executive_upd());
    },
    CliErrorHandlerService::class => static function () {
        return new CliErrorHandlerService(
            new ConsoleOutput(),
            ee()->lang
        );
    },
    CliQuestionService::class => static function () {
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
    CommandsService::class => static function () {
        return new CommandsService(
            ee()->config,
            ee('Addon'),
            new CommandModelFactory()
        );
    },
    EETemplateService::class => static function () {
        return new EETemplateService();
    },
    DeleteSnippetsNotOnDiskService::class => static function () {
        return new DeleteSnippetsNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            ExecutiveDi::get('eeSiteShortNames'),
            ee('Model'),
            new Filesystem()
        );
    },
    DeleteTemplateGroupsWithoutTemplatesService::class => static function () {
        return new DeleteTemplateGroupsWithoutTemplatesService(ee('Model'));
    },
    DeleteTemplatesNotOnDiskService::class => static function () {
        return new DeleteTemplatesNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            ExecutiveDi::get('eeSiteShortNames'),
            ee('Model'),
            new Filesystem()
        );
    },
    DeleteVariablesNotOnDiskService::class => static function () {
        return new DeleteVariablesNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            ExecutiveDi::get('eeSiteShortNames'),
            ee('Model'),
            new Filesystem()
        );
    },
    ElevateSessionService::class => static function () {
        return new ElevateSessionService(
            new QueryBuilderFactory(),
            ee()->session,
            ee()->router,
            ee()->load
        );
    },
    EnsureIndexTemplatesExistService::class => static function () {
        return new EnsureIndexTemplatesExistService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            new FinderFactory(),
            new Filesystem()
        );
    },
    ExtensionDesignerService::class => static function () {
        return new ExtensionDesignerService(ee('Model'), ee('db'));
    },
    ForceSnippetVarSyncToDatabaseService::class => static function () {
        return new ForceSnippetVarSyncToDatabaseService(ee('Model'));
    },
    GetNextQueueItemService::class => static function () {
        return new GetNextQueueItemService(new QueryBuilderFactory());
    },
    LayoutDesignerService::class => static function () {
        return new LayoutDesignerService(ee('Model'));
    },
    MarkAsStoppedDueToErrorService::class => static function () {
        return new MarkAsStoppedDueToErrorService(new QueryBuilderFactory());
    },
    MarkQueueItemAsRunService::class => static function () {
        return new MarkQueueItemAsRunService(
            new QueryBuilderFactory(),
            ExecutiveDi::get(UpdateActionQueueStatusService::class)
        );
    },
    MigrationsService::class => static function () {
        return new MigrationsService(
            ee('Filesystem'),
            new QueryBuilderFactory()
        );
    },
    QueueApi::class => static function () {
        return new QueueApi(new ExecutiveDi());
    },
    RoutingService::class => static function () {
        /** @var EE_Config $config */
        $config = ee()->config;
        $routes = $config->item('customRoutes');

        /** @var EE_Loader $loader */
        $loader = ee()->load;
        $loader->library('template', null, 'TMPL');

        return new RoutingService(
            ExecutiveDi::get(RouteModel::SINGLETON_DI_NAME),
            is_array($routes) ? $routes : [],
            ee()->lang,
            new ExecutiveDi(),
            ee()->TMPL,
            ee()->config,
            new SapiEmitter()
        );
    },
    RunCommandService::class => static function () {
        return new RunCommandService(
            ExecutiveDi::get(CliArgumentsModel::class),
            new ExecutiveDi(),
            new EeDiFactory(),
            new ClosureFromCallableFactory(),
            new ReflectionFunctionFactory()
        );
    },
    ScheduleService::class => static function () {
        return new ScheduleService(
            ee()->config,
            ee('Addon'),
            ExecutiveDi::get(CommandsService::class),
            new ScheduleItemModelFactory(),
            new QueryBuilderFactory(),
            new CliArgumentsModelFactory()
        );
    },
    SyncTemplatesFromFilesService::class => static function () {
        return new SyncTemplatesFromFilesService();
    },
    TemplateMakerService::class => static function () {
        return new TemplateMakerService(
            new SplFileInfoFactory(),
            new Filesystem()
        );
    },
    UpdateActionQueueStatusService::class => static function () {
        return new UpdateActionQueueStatusService(new QueryBuilderFactory());
    },
    ViewService::class => static function () {
        /** @var EE_Config $config */
        $config        = ee()->config;
        $viewsBasePath = $config->item('cpViewsBasePath');

        return new ViewService(
            ee('executive:Provider'),
            is_string($viewsBasePath) ? $viewsBasePath : ''
        );
    },
    ViewService::INTERNAL_DI_NAME => static function () {
        return new ViewService(
            ee('executive:Provider'),
            __DIR__ . '/views'
        );
    },

    /**
     * Twig Extensions
     */
    EETemplateTwigExtension::class => static function () {
        return new EETemplateTwigExtension(
            ExecutiveDi::get(EETemplateService::class)
        );
    },

    /**
     * Other
     */
    'eeSiteShortNames' => static function () {
        $sites = ee('Model')->get('Site')
            ->fields('site_name')
            ->all();

        $sitesArray = [];

        foreach ($sites as $site) {
            $sitesArray[$site->site_id] = $site->site_name;
        }

        return $sitesArray;
    },
    TwigEnvironment::class => static function () {
        return (new TwigFactory())->get();
    },
];
