<?php

declare(strict_types=1);

use buzzingpixel\executive\controllers\ConsoleController;
use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;
use buzzingpixel\executive\exceptions\InvalidActionException;
use buzzingpixel\executive\exceptions\InvalidRouteConfiguration;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\CliErrorHandlerService;
use buzzingpixel\executive\services\ElevateSessionService;
use buzzingpixel\executive\services\RoutingService;
use DI\DependencyException;
use DI\NotFoundException;
use EllisLab\ExpressionEngine\Core\Request;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;

class Executive_ext
{
    /** @var string $version */
    public $version = EXECUTIVE_VER;

    /**
     * session_start extension
     *
     * @throws Exception
     */
    public function sessions_start() : void
    {
        if (! defined('REQ') || REQ !== 'CONSOLE') {
            return;
        }

        /** @var EE_Config $configService */
        $configService = ee()->config;
        $configService->set_item('disable_csrf_protection', 'y');

        /** @var CliErrorHandlerService $cliErrorHandlerService */
        $cliErrorHandlerService = ExecutiveDi::make(
            CliErrorHandlerService::class
        );

        $cliErrorHandlerService->register();
    }

    /**
     * core_boot extension
     *
     * @throws Exception
     */
    public function core_boot() : void
    {
        /** @var EE_Lang $lang */
        $lang = ee()->lang;

        if (! defined('REQ') || REQ !== 'CONSOLE') {
            /**
             * Note a CLI request, check to see if action request
             */

            /** @var Request $request */
            $request = ee('Request');

            /** @var EE_Config $config */
            $config = ee()->config;

            $actionKey = $request->post('action') ?: $request->get('action');

            if ($actionKey === null) {
                return;
            }

            $actions = $config->item('actions');

            if (! is_array($actions) || ! $config->item('actions')) {
                $lang->loadfile('executive');
                throw new InvalidActionException(
                    $lang->line('noActionsSpecified')
                );
            }

            if (! isset($actions[$actionKey])) {
                $lang->loadfile('executive');
                throw new InvalidActionException(
                    str_replace(
                        '{{action}}',
                        $actionKey,
                        $lang->line('actionConfigNotFound')
                    )
                );
            }

            $actionConfig = $actions[$actionKey];

            if (! isset($actionConfig['class'])) {
                $lang->loadfile('executive');
                throw new InvalidActionException(
                    str_replace(
                        '{{action}}',
                        $actionKey,
                        $lang->line('actionClassNotSet')
                    )
                );
            }

            if (! isset($actionConfig['method'])) {
                $lang->loadfile('executive');
                throw new InvalidActionException(
                    str_replace(
                        '{{action}}',
                        $actionKey,
                        $lang->line('actionMethodNotSet')
                    )
                );
            }

            if (! class_exists($actionConfig['class'])) {
                $lang->loadfile('executive');
                throw new InvalidActionException(
                    str_replace(
                        '{{action}}',
                        $actionKey,
                        lang('actionClassNotFound')
                    )
                );
            }

            try {
                $class = ExecutiveDi::make($actionConfig['class']);
            } catch (Throwable $e) {
                $class = new $actionConfig['class']();
            }

            if (! method_exists($class, $actionConfig['method'])) {
                $lang->loadfile('executive');
                throw new InvalidActionException(
                    str_replace(
                        '{{action}}',
                        $actionKey,
                        $lang->line('actionMethodNotFound')
                    )
                );
            }

            $class->{$actionConfig['method']}();

            return;
        }

        /**
         * This is a CLI request
         */

        // Make sure the lang file is loaded
        $lang->loadfile('executive');

        /** @var ElevateSessionService $elevateSessionService */
        $elevateSessionService = ExecutiveDi::make(ElevateSessionService::class);
        $elevateSessionService->run();

        // Prevent timeout (hopefully)
        @set_time_limit(0);

        // Run the console controller and catch any errors that bubble up
        try {
            /** @var ConsoleController $consoleController */
            $consoleController = ExecutiveDi::make(ConsoleController::class);
            $consoleController->run();
        } catch (Throwable $e) {
            /** @var CliErrorHandlerService $cliErrorHandlerService */
            $cliErrorHandlerService = ExecutiveDi::make(
                CliErrorHandlerService::class
            );

            $cliErrorHandlerService->exceptionHandler($e);
        }

        // Make sure we exit here
        exit;
    }

    /**
     * Runs routing if applicable
     *
     * @return mixed
     *
     * @throws NotFoundException
     * @throws DependencyException
     * @throws InvalidRouteConfiguration
     * @throws DependencyInjectionBuilderException
     */
    public function core_template_route(string $uri)
    {
        /** @var EE_Extensions $extensions */
        $extensions = ee()->extensions;

        /** @var RoutingService $routingService */
        $routingService = ExecutiveDi::make(RoutingService::class);

        $result = $routingService->routeUri($uri);

        if (! $result) {
            return $extensions->last_call;
        }

        $extensions->end_script = true;

        return $result;
    }

    /**
     * Routes user extensions
     *
     * @param array $args
     */
    public function __call(string $name, array $args) : void
    {
        if (mb_stripos($name, 'userExtensionRouting') !== 0) {
            return;
        }

        $idFind = explode('__', $name);

        if (! isset($idFind[1]) || ! is_numeric($idFind[1])) {
            return;
        }

        $id = (int) $idFind[1];

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = ee('db');

        $row = $queryBuilder->where('id', $id)
            ->get('executive_user_extensions')
            ->row();

        if (! $row) {
            return;
        }

        try {
            $class = ExecutiveDi::make($row->class);
        } catch (Throwable $e) {
            $class = new $row->class();
        }

        call_user_func_array([$class, $row->method], $args);
    }
}
