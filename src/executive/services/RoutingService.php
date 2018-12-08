<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use EE_Lang;
use Throwable;
use EE_Config;
use EE_Template;
use Psr\Http\Message\ResponseInterface;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\models\RouteModel;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use buzzingpixel\executive\exceptions\InvalidRouteConfiguration;

class RoutingService
{
    private $routeModel;
    private $routes;
    private $lang;
    private $executiveDi;
    private $template;
    private $config;
    private $emitter;

    private $anyRouteMatched = false;
    private $lastResponse;

    public function __construct(
        RouteModel $routeModel,
        array $routes,
        EE_Lang $lang,
        ExecutiveDi $executiveDi,
        EE_Template $template,
        EE_Config $config,
        SapiEmitter $emitter
    ) {
        $this->routeModel = $routeModel;
        $this->routes = $routes;
        $this->lang = $lang;
        $this->executiveDi = $executiveDi;
        $this->template = $template;
        $this->config = $config;
        $this->emitter = $emitter;
    }

    /**
     * @throws InvalidRouteConfiguration
     */
    public function routeUri(string $uri): array
    {
        if (! $this->routes) {
            return [];
        }

        $after = $this->routes[':after'] ?? null;
        $catch = $this->routes[':catch'] ?? null;

        if ($after) {
            unset($this->routes[':after']);
        }

        if ($catch) {
            unset($this->routes[':catch']);
        }

        if (isset($this->routes[':before'])) {
            $this->runRule($uri, ':before', $this->routes[':before']);

            unset($this->routes[':before']);

            if ($this->routeModel->getStop()) {
                return $this->respond();
            }
        }

        foreach ($this->routes as $rule => $params) {
            $this->runRule($uri, $rule, $params);

            if ($this->routeModel->getStop()) {
                return $this->respond();
            }
        }

        if ($after) {
            $this->runRule($uri, ':after', $after);

            if ($this->routeModel->getStop()) {
                return $this->respond();
            }
        }

        if (! $this->anyRouteMatched && $catch) {
            $this->runRule($uri, ':catch', $catch);

            if ($this->routeModel->getStop()) {
                return $this->respond();
            }
        }

        if ($this->lastResponse) {
            $this->emitter->emit($this->lastResponse);
            exit();
        }

        return $this->respond();
    }

    /**
     * Responds based on RouteModel
     * @return array
     */
    private function respond(): array
    {
        if ($this->routeModel->get404()) {
            $this->template->show_404();
            return [];
        }

        $this->config->_global_vars = array_merge(
            $this->config->_global_vars,
            $this->routeModel->getVariables()
        );

        if (! $this->routeModel->hasTemplate()) {
            return [];
        }

        return explode('/', $this->routeModel->getTemplate());
    }

    /**
     * Runs a rule
     * @param string $uri
     * @param string $rule
     * @param array $params
     * @throws InvalidRouteConfiguration;
     */
    private function runRule(string $uri, string $rule, array $params): void
    {
        if (! isset($params['class'])) {
            $this->throwInvalidRouteConfigurationException(
                $rule,
                'routeClassNotSet'
            );
        }

        if (! isset($params['method'])) {
            $this->throwInvalidRouteConfigurationException(
                $rule,
                'routeMethodNotSet'
            );
        }

        $regex = ltrim(rtrim($rule === ':home' ? '' : $rule, '/'), '/');
        $match = [];

        if (! \in_array($regex, [
            ':before',
            ':after',
            ':catch'
        ])) {
            if (strpos($regex, ':') !== false) {
                $regex = str_replace(
                    [
                        ':any',
                        ':num',
                        ':year',
                        ':month',
                        ':day',
                        '/:pagination',
                        ':pagination',
                        '/:all',
                    ],
                    [
                        '([^/]+)',
                        '(\d+)',
                        '(\d{4})',
                        '(\d{2})',
                        '(\d{2})',
                        '((?:/P\d+)?)',
                        '((?:/P\d+)?)',
                        '((?:/.*)?)',
                    ],
                    $regex
                );
            }

            if (! preg_match('#^' . trim($regex, '/') . '$#', $uri, $match)) {
                return;
            }

            array_shift($match);

            $this->anyRouteMatched = true;
        }

        try {
            $class = $this->executiveDi->makeFromDefinition($params['class']);
        } catch (Throwable $e) {
            $class = $params['class'];

            if (! class_exists($class)) {
                $this->throwInvalidRouteConfigurationException(
                    $rule,
                    'routeClassNotFound',
                    $e
                );
            }

            try {
                $class = new $class();
            } catch (Throwable $e) {
                $this->throwInvalidRouteConfigurationException(
                    $rule,
                    'routeClassCouldNotBeConstructed',
                    $e
                );
            }
        }

        $method = $params['method'];

        if (! method_exists($class, $params['method'])) {
            $this->throwInvalidRouteConfigurationException(
                $rule,
                'routeMethodNotFound'
            );
        }

        $arguments = array_merge([$this->routeModel], $match);

        $response = \call_user_func_array([$class, $method], $arguments);

        if ($response instanceof ResponseInterface) {
            $this->lastResponse = $response;
            $this->routeModel->setResponse($this->lastResponse);
        }
    }

    /**
     * @throws InvalidRouteConfiguration
     */
    private function throwInvalidRouteConfigurationException(
        string $routeKey,
        string $langKey,
        Throwable $previous = null
    ): void {
        $this->lang->loadfile('executive');

        throw new InvalidRouteConfiguration(
            str_replace(
                '{{routeKey}}',
                $routeKey,
                $this->lang->line($langKey)
            ),
            500,
            $previous
        );
    }
}
