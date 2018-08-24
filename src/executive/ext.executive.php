<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use DI\NotFoundException;
use DI\DependencyException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\RoutingService;
use buzzingpixel\executive\controllers\ConsoleController;
use buzzingpixel\executive\services\ElevateSessionService;
use buzzingpixel\executive\services\CliErrorHandlerService;
use buzzingpixel\executive\exceptions\InvalidRouteConfiguration;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;
use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;

/**
 * Class Executive_ext
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
// @codingStandardsIgnoreStart
class Executive_ext
// @codingStandardsIgnoreEnd
{
    /** @var string $version */
    public $version = EXECUTIVE_VER;

    /**
     * session_start extension
     * @throws \Exception
     */
    // @codingStandardsIgnoreStart
    public function sessions_start(): void // @codingStandardsIgnoreEnd
    {
        if (! defined('REQ') || REQ !== 'CONSOLE') {
            return;
        }

        /** @var \EE_Config $configService */
        $configService = ee()->config;
        $configService->set_item('disable_csrf_protection', 'y');

        /** @var CliErrorHandlerService $cliErrorHandlerService */
        $cliErrorHandlerService = ExecutiveDi::get(
            CliErrorHandlerService::class
        );

        $cliErrorHandlerService->register();
    }

    /**
     * core_boot extension
     * @throws \Exception
     */
    // @codingStandardsIgnoreStart
    public function core_boot(): void // @codingStandardsIgnoreEnd
    {
        if (! defined('REQ') || REQ !== 'CONSOLE') {
            return;
        }

        // Make sure the lang file is loaded
        ee()->lang->loadfile('executive');

        /** @var ElevateSessionService $elevateSessionService */
        $elevateSessionService = ExecutiveDi::get(ElevateSessionService::class);
        $elevateSessionService->run();

        // Prevent timeout (hopefully)
        @set_time_limit(0);

        /** @var ConsoleController $consoleController */
        $consoleController = ExecutiveDi::get(ConsoleController::class);

        // Run the console controller
        $consoleController->run();

        // Make sure we exit here
        exit;
    }

    /**
     * Runs routing if applicable
     * @param string $uri
     * @return mixed
     * @throws NotFoundException
     * @throws DependencyException
     * @throws InvalidRouteConfiguration
     * @throws DependencyInjectionBuilderException
     */
    // @codingStandardsIgnoreStart
    public function core_template_route(string $uri) // @codingStandardsIgnoreEnd
    {
        /** @var EE_Extensions $extensions */
        $extensions = ee()->extensions;

        /** @var RoutingService $routingService */
        $routingService = ExecutiveDi::get(RoutingService::class);

        $result = $routingService->routeUri($uri);

        if (! $result) {
            return $extensions->last_call;
        }

        $extensions->end_script = true;

        return $result;
    }

    /**
     * Routes user extensions
     * @param string $name
     * @param array $args
     */
    public function __call($name, $args)
    {
        if (stripos($name, 'userExtensionRouting') !== 0) {
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
            $class = ExecutiveDi::get($row->class);
        } catch (\Throwable $e) {
            $class = new $row->class();
        }

        call_user_func_array([$class, $row->method], $args);
    }
}
