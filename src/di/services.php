<?php

declare(strict_types=1);

use buzzingpixel\executive\models\RouteModel;
use buzzingpixel\executive\services\CaseConversionService;
use buzzingpixel\executive\services\CliErrorHandlerService;
use buzzingpixel\executive\services\CliInstallService;
use buzzingpixel\executive\services\CliQuestionService;
use buzzingpixel\executive\services\CommandsService;
use buzzingpixel\executive\services\ConditionalSapiStreamEmitter;
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
use Symfony\Component\Filesystem\Filesystem;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use function DI\autowire;

return [
    AddToQueueService::class => autowire(),
    CaseConversionService::class => autowire(),
    CliInstallService::class => autowire(),
    CliErrorHandlerService::class => autowire(),
    CliQuestionService::class => autowire(),
    CommandsService::class => autowire(),
    ConditionalSapiStreamEmitter::class => static function (ContainerInterface $di) {
        return new ConditionalSapiStreamEmitter(
            $di->get(SapiStreamEmitter::class),
            8192
        );
    },
    EETemplateService::class => autowire(),
    DeleteSnippetsNotOnDiskService::class => static function (ContainerInterface $di) {
        return new DeleteSnippetsNotOnDiskService(
            rtrim(PATH_TMPL, DIRECTORY_SEPARATOR),
            $di->get('eeSiteShortNames'),
            ee('Model'),
            new Filesystem()
        );
    },
    DeleteTemplateGroupsWithoutTemplatesService::class => autowire(),
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
    ElevateSessionService::class => autowire(),
    EnsureIndexTemplatesExistService::class => autowire()
        ->constructorParameter('templatesPath', rtrim(PATH_TMPL, DIRECTORY_SEPARATOR)),
    ForceSnippetVarSyncToDatabaseService::class => autowire(),
    GetNextQueueItemService::class => autowire(),
    MarkAsStoppedDueToErrorService::class => autowire(),
    MarkQueueItemAsRunService::class => autowire(),
    MigrationsService::class => autowire(),
    QueueApi::class => autowire(),
    RoutingService::class => static function (ContainerInterface $di) {
        $config = $di->get(EE_Config::class);

        $routes = $config->item('customRoutes');

        return new RoutingService(
            $di->get(RouteModel::class),
            is_array($routes) ? $routes : [],
            $di->get(EE_Lang::class),
            $di,
            $di->get(EE_Template::class),
            $config,
            $di->get(EmitterStack::class)
        );
    },
    RunCommandService::class => autowire(),
    ScheduleService::class => autowire(),
    SyncTemplatesFromFilesService::class => autowire(),
    TemplateMakerService::class => autowire(),
    UpdateActionQueueStatusService::class => autowire(),
    ViewService::class => static function (ContainerInterface $di) {
        $config = $di->get(EE_Config::class);

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
