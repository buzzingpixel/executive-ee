<?php

declare(strict_types=1);

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
use buzzingpixel\executive\models\CliArgumentsModel;
use buzzingpixel\executive\models\RouteModel;
use buzzingpixel\executive\services\CaseConversionService;
use buzzingpixel\executive\services\CliErrorHandlerService;
use buzzingpixel\executive\services\CliInstallService;
use buzzingpixel\executive\services\CliQuestionService;
use buzzingpixel\executive\services\CommandsService;
use buzzingpixel\executive\services\EETemplateService;
use buzzingpixel\executive\services\ElevateSessionService;
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
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

return [
    AddToQueueService::class => static function () {
        return new AddToQueueService(
            new QueryBuilderFactory()
        );
    },
    CaseConversionService::class => static function () {
        return new CaseConversionService();
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
            (new ConsoleApplication())
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
    DeleteSnippetsNotOnDiskService::class => static function (ContainerInterface $di) {
        return new DeleteSnippetsNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            $di->get('eeSiteShortNames'),
            ee('Model'),
            new Filesystem()
        );
    },
    DeleteTemplateGroupsWithoutTemplatesService::class => static function () {
        return new DeleteTemplateGroupsWithoutTemplatesService(ee('Model'));
    },
    DeleteTemplatesNotOnDiskService::class => static function (ContainerInterface $di) {
        return new DeleteTemplatesNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            $di->get('eeSiteShortNames'),
            ee('Model'),
            new Filesystem()
        );
    },
    DeleteVariablesNotOnDiskService::class => static function (ContainerInterface $di) {
        return new DeleteVariablesNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            $di->get('eeSiteShortNames'),
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
    ForceSnippetVarSyncToDatabaseService::class => static function () {
        return new ForceSnippetVarSyncToDatabaseService(
            ee('Model')
        );
    },
    GetNextQueueItemService::class => static function () {
        return new GetNextQueueItemService(
            new QueryBuilderFactory()
        );
    },
    MarkAsStoppedDueToErrorService::class => static function () {
        return new MarkAsStoppedDueToErrorService(
            new QueryBuilderFactory()
        );
    },
    MarkQueueItemAsRunService::class => static function (ContainerInterface $di) {
        return new MarkQueueItemAsRunService(
            new QueryBuilderFactory(),
            $di->get(UpdateActionQueueStatusService::class)
        );
    },
    MigrationsService::class => static function () {
        return new MigrationsService(
            ee('Filesystem'),
            new QueryBuilderFactory()
        );
    },
    QueueApi::class => static function (ContainerInterface $di) {
        return new QueueApi($di);
    },
    RoutingService::class => static function (ContainerInterface $di) {
        /** @var EE_Config $config */
        $config = ee()->config;
        $routes = $config->item('customRoutes');

        /** @var EE_Loader $loader */
        $loader = ee()->load;
        $loader->library('template', null, 'TMPL');

        return new RoutingService(
            $di->get(RouteModel::class),
            is_array($routes) ? $routes : [],
            ee()->lang,
            $di,
            ee()->TMPL,
            ee()->config,
            new SapiEmitter()
        );
    },
    RunCommandService::class => static function (ContainerInterface $di) {
        return new RunCommandService(
            $di->get(CliArgumentsModel::class),
            $di,
            new EeDiFactory(),
            new ClosureFromCallableFactory(),
            new ReflectionFunctionFactory()
        );
    },
    ScheduleService::class => static function (ContainerInterface $di) {
        return new ScheduleService(
            ee()->config,
            ee('Addon'),
            $di->get(CommandsService::class),
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
        return new UpdateActionQueueStatusService(
            new QueryBuilderFactory()
        );
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
];
