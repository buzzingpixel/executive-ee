<?php

declare(strict_types=1);

namespace buzzingpixel\executive\models;

use buzzingpixel\executive\abstracts\ModelAbstract;
use Psr\Http\Message\ResponseInterface;
use function array_merge;

class RouteModel extends ModelAbstract
{
    public const SINGLETON_DI_NAME = self::class . 'Singleton';

    /** @var string $template */
    private $template = '';

    public function setTemplate(string $val) : self
    {
        $this->template = $val;

        return $this;
    }

    public function getTemplate() : string
    {
        return $this->template;
    }

    public function hasTemplate() : bool
    {
        return ! empty($this->template);
    }

    /** @var bool $send404 */
    private $send404 = false;

    public function set404(bool $val = true) : self
    {
        $this->send404 = $val;

        return $this;
    }

    public function get404() : bool
    {
        return $this->send404;
    }

    /** @var bool $stop */
    private $stop = false;

    public function setStop(bool $val = true) : self
    {
        $this->stop = $val;

        return $this;
    }

    public function getStop() : bool
    {
        return $this->stop;
    }

    /** @var array $variables */
    private $variables = [];

    public function setVariable(string $name, $val) : self
    {
        $this->variables[$name] = $val;

        return $this;
    }

    public function setVariables(array $vars) : self
    {
        $this->variables = array_merge($this->variables, $vars);

        return $this;
    }

    public function getVariable(string $name)
    {
        return $this->variables[$name] ?? null;
    }

    public function getVariables() : array
    {
        return $this->variables;
    }

    /** @var array $pairs */
    private $pairs = [];

    public function setPair(string $name, array $vars) : self
    {
        $this->pairs[$name] = $vars;

        return $this;
    }

    public function getPair(string $name) : ?array
    {
        return $this->pairs[$name] ?? null;
    }

    public function getPairs() : array
    {
        return $this->pairs;
    }

    /** @var ResponseInterface $response */
    private $response;

    public function getResponse() : ?ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response) : self
    {
        $this->response = $response;

        return $this;
    }
}
