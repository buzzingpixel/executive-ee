<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\models;

use buzzingpixel\executive\abstracts\ModelAbstract;
use buzzingpixel\executive\exceptions\InvalidCommandCallable;

/**
 * Class CommandModel
 */
class CommandModel extends ModelAbstract
{
    /** @var string $name */
    private $name = '';

    /**
     * Sets name property
     * @param string $val
     * @return CommandModel
     */
    public function setName(string $val): self
    {
        $this->name = $val;
        return $this;
    }

    /**
     * Gets name property
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /** @var callable $callable */
    public $callable = [self::class, 'commandCallableNotSpecified'];

    /**
     * Default callable called when a command's callable is not found
     * @throws InvalidCommandCallable
     */
    public static function commandCallableNotSpecified(): void
    {
        throw new InvalidCommandCallable(lang('commandCallableNotSpecified'));
    }

    /**
     * Sets callable property
     * @param callable $val
     * @return CommandModel
     */
    public function setCallable(callable $val): self
    {
        $this->callable = $val;
        return $this;
    }

    /**
     * Gets callable property
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }

    /** @var string $class */
    public $class = '';

    /**
     * Sets class property
     * @param string $val
     * @return CommandModel
     */
    public function setClass(string $val): self
    {
        $this->class = $val;
        return $this;
    }

    /**
     * Gets class property
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /** @var string $method */
    public $method = '';

    /**
     * Sets method property
     * @param string $val
     * @return CommandModel
     */
    public function setMethod(string $val): self
    {
        $this->method = $val;
        return $this;
    }

    /**
     * Gets method property
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /** @var string $description */
    public $description = '';

    /**
     * Sets description property
     * @param string $val
     * @return CommandModel
     */
    public function setDescription(string $val): self
    {
        $this->description = $val;
        return $this;
    }

    /**
     * Gets description property
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /** @var CliArgumentsModel $customCliArgumentsModel */
    private $customCliArgumentsModel;

    /**
     * Sets a custom CLI Arguments Model
     * @param CliArgumentsModel $model
     * @return CommandModel
     */
    public function setCustomCliArgumentsModel(CliArgumentsModel $model): self
    {
        $this->customCliArgumentsModel = $model;
        return $this;
    }

    /**
     * Checks if there's a custom CLI Arguments Model
     * @return bool
     */
    public function hasCustomCliArgumentsModel(): bool
    {
        return $this->customCliArgumentsModel !== null;
    }

    /**
     * Gets custom CLI Arguments Model
     * @return CliArgumentsModel
     */
    public function getCustomCliArgumentsModel(): CliArgumentsModel
    {
        return $this->customCliArgumentsModel;
    }
}
