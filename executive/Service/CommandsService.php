<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Service;

use BuzzingPixel\DataModel\ModelCollection;
use BuzzingPixel\Executive\BaseComponent;
use BuzzingPixel\DataModel\DataType;
use BuzzingPixel\Executive\Model\ArgumentsModel;
use BuzzingPixel\Executive\Model\CommandGroupModel;
use BuzzingPixel\Executive\Model\CommandModel;
use EllisLab\ExpressionEngine\Service\Addon\Factory as EEAddonFactory;
use EllisLab\ExpressionEngine\Service\Addon\Addon as EEAddon;
use BuzzingPixel\Executive\Service\ConsoleService;

/**
 * Class CommandsService
 * @property-read ModelCollection $commandGroups
 *
 * # Following properties for internal use only
 * @property-read EEAddonFactory $eeAddonFactory Internal use only
 * @property-read \EE_Config $eeConfigService
 * @property-read ConsoleService $consoleService
 */
class CommandsService extends BaseComponent
{
    /** @var ModelCollection $commandGroupStorage */
    private $commandGroupStorage;

    /**
     * @inheritdoc
     */
    public function defineAttributes()
    {
        return array(
            'eeAddonFactory' => array(
                'type' => DataType::INSTANCE,
                'expect' => '\EllisLab\ExpressionEngine\Service\Addon\Factory',
            ),
            'eeConfigService' => array(
                'type' => DataType::INSTANCE,
                'expect' => '\EE_Config',
            ),
            'consoleService' => array(
                'type' => DataType::INSTANCE,
                'expect' => '\BuzzingPixel\Executive\Service\ConsoleService',
            ),
        );
    }

    /**
     * Get command groups
     * @return ModelCollection
     */
    public function getCommandGroups()
    {
        if ($this->commandGroupStorage !== null) {
            return $this->commandGroupStorage;
        }

        $commandGroups = new ModelCollection();

        if ($userCommands = $this->eeConfigService->item('commands')) {
            /** @var array $userCommands */

            $commands = array();

            foreach ($userCommands as $name => $details) {
                $commands[] = new CommandModel(array_merge(
                    array(
                        'name' => $name
                    ),
                    $details
                ));
            }

            $commandGroup = new CommandGroupModel(array(
                'name' => 'user',
                'commands' => new ModelCollection($commands),
            ));

            $commandGroups->addModel($commandGroup);
        }

        foreach ($this->eeAddonFactory->all() as $addon) {
            /** @var EEAddon $addon */

            $provider = $addon->getProvider();

            if (! $addon->isInstalled() || ! $provider->get('commands')) {
                continue;
            }

            $groupName = $provider->getPrefix();
            $commands = array();
            $addonCommands = $provider->get('commands');

            foreach ($addonCommands as $name => $details) {
                $commands[] = new CommandModel(array_merge(
                    array(
                        'name' => $name
                    ),
                    $details
                ));
            }

            $commandGroup = new CommandGroupModel(array(
                'name' => $groupName,
                'commands' => new ModelCollection($commands),
            ));

            $commandGroups->addModel($commandGroup);
        }

        $this->commandGroupStorage = $commandGroups;

        return $commandGroups;
    }

    /**
     * Get command
     * @param string $group
     * @param string $command
     * @return CommandModel|null
     */
    public function getCommand($group, $command)
    {
        foreach ($this->commandGroups as $groupModel) {
            /** @var CommandGroupModel $groupModel */

            if ($groupModel->name !== $group) {
                continue;
            }

            foreach ($groupModel->commands as $commandModel) {
                /** @var CommandModel $commandModel */

                if ($commandModel->name !== $command) {
                    continue;
                }

                return $commandModel;
            }
        }

        return null;
    }

    /**
     * Run command
     * @param CommandModel $commandModel
     * @param ArgumentsModel $argumentsModel
     * @throws \Exception
     */
    public function runCommand(
        CommandModel $commandModel,
        ArgumentsModel $argumentsModel
    ) {
        $class = $commandModel->class;
        $method = $commandModel->method;

        if (! class_exists($class)) {
            $this->consoleService->writeLn(lang('classNotFound'), 'red');
            return;
        }

        if (! method_exists($class, $method)) {
            $this->consoleService->writeLn(lang('classMethodNotFound'), 'red');
            return;
        }

        $rMethod = new \ReflectionMethod($class, $method);
        $rArguments = $rMethod->getParameters();

        $argsArray = array();

        foreach ($rArguments as $rArgument) {
            $argsArray[] = $argumentsModel->getArgument($rArgument->name);
        }

        call_user_func_array(
            array(
                new $class,
                $method,
            ),
            $argsArray
        );
    }
}
