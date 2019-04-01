<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\exceptions\InvalidRouteConfiguration;
use buzzingpixel\executive\models\RouteModel;
use EE_Config;
use EE_Lang;
use EE_Template;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use function array_merge;
use function array_shift;
use function call_user_func_array;
use function class_exists;
use function explode;
use function in_array;
use function is_array;
use function ltrim;
use function mb_strpos;
use function method_exists;
use function preg_match;
use function rtrim;
use function str_replace;
use function trim;

class RoutingService
{
    /** @var RouteModel $routeModel */
    private $routeModel;
    /** @var array $routes */
    private $routes;
    /** @var EE_Lang $lang */
    private $lang;
    /** @var ContainerInterface $di */
    private $di;
    /** @var EE_Template $template */
    private $template;
    /** @var EE_Config $config */
    private $config;
    /** @var EmitterStack $emitterStack */
    private $emitterStack;

    /** @var bool $anyRouteMatched */
    private $anyRouteMatched = false;

    /** @var mixed $lastResponse */
    private $lastResponse;

    public function __construct(
        RouteModel $routeModel,
        array $routes,
        EE_Lang $lang,
        ContainerInterface $di,
        EE_Template $template,
        EE_Config $config,
        EmitterStack $emitterStack
    ) {
        $this->routeModel   = $routeModel;
        $this->routes       = $routes;
        $this->lang         = $lang;
        $this->di           = $di;
        $this->template     = $template;
        $this->config       = $config;
        $this->emitterStack = $emitterStack;
    }

    /**
     * @throws InvalidRouteConfiguration
     */
    public function routeUri(string $uri) : array
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
            $this->emitterStack->emit($this->lastResponse);
            exit;
        }

        return $this->respond();
    }

    /**
     * Responds based on RouteModel
     */
    private function respond() : array
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
     *
     * @throws InvalidRouteConfiguration
     */
    private function runRule(string $uri, string $rule, $params) : void
    {
        if (! is_array($params)) {
            $params = ['class' => $params];
        }

        if (! isset($params['class'])) {
            $this->throwInvalidRouteConfigurationException(
                $rule,
                'routeClassNotSet'
            );
        }

        if (! isset($params['method'])) {
            $params['method'] = '__invoke';
        }

        $regex = ltrim(rtrim($rule === ':home' ? '' : $rule, '/'), '/');
        $match = [];

        if (! in_array($regex, [
            ':before',
            ':after',
            ':catch',
        ])) {
            if (mb_strpos($regex, ':') !== false) {
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
            $class = $this->di->get($params['class']);
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

        $response = call_user_func_array([$class, $method], $arguments);

        if (! ($response instanceof ResponseInterface)) {
            return;
        }

        $this->lastResponse = $response;
        $this->routeModel->setResponse($this->lastResponse);
    }

    /**
     * @throws InvalidRouteConfiguration
     */
    private function throwInvalidRouteConfigurationException(
        string $routeKey,
        string $langKey,
        ?Throwable $previous = null
    ) : void {
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
