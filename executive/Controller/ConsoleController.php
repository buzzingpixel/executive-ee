<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Controller;

use BuzzingPixel\Executive\BaseComponent;
use BuzzingPixel\Executive\Model\CommandGroupModel;
use BuzzingPixel\Executive\Model\CommandModel;
use BuzzingPixel\Executive\Service\ArgsService;
use BuzzingPixel\Executive\Model\ArgumentsModel;
use BuzzingPixel\Executive\Service\CommandsService;
use BuzzingPixel\Executive\Service\ConsoleService;

/**
 * Class ConsoleController
 */
class ConsoleController extends BaseComponent
{
    /** @var ArgumentsModel $args */
    private $args;

    /** @var ConsoleService $consoleService */
    private $consoleService;

    /** @var CommandsService $commandsService */
    private $commandsService;

    /**
     * Init
     */
    public function init()
    {
        /** @var ArgsService $argsService */
        $argsService = ee('executive:ArgsService');

        $this->args = $argsService->parseRawArgs(EXECUTIVE_RAW_ARGS);
        $this->consoleService = ee('executive:ConsoleService');
        $this->commandsService = ee('executive:CommandsService');
    }

    /**
     * Run console request
     * @throws \Exception
     */
    public function runConsoleRequest()
    {
        if (($group = $this->args->getArgument('group')) === null) {
            $this->listCommands();
            return;
        }

        if (($command = $this->args->getArgument('command')) === null) {
            $this->consoleService->writeLn(lang('mustSpecifyCommand'), 'red');
            return;
        }

        $commandModel = $this->commandsService->getCommand($group, $command);

        if (! $commandModel) {
            $this->consoleService->writeLn(lang('commandNotFound'), 'red');
        }

        $this->commandsService->runCommand($commandModel, $this->args);
    }

    /**
     * List commands
     */
    private function listCommands()
    {
        // Show usage
        $this->consoleService->writeLn(lang('executiveCommandLine') . ' ', null, false);
        $this->consoleService->writeLn(EXECUTIVE_VER, 'green');
        $this->consoleService->writeLn('');
        $this->consoleService->writeLn(lang('usage:'), 'yellow');
        $this->consoleService->writeLn('  ' . lang('usageExample'));

        $toCharacters = 0;

        foreach ($this->commandsService->commandGroups as $group) {
            /** @var CommandGroupModel $group */
            foreach ($group->commands as $command) {
                /** @var CommandModel $command */
                $len = strlen($command->name);
                $toCharacters = $len > $toCharacters ? $len : $toCharacters;
            }
        }

        $toCharacters += 2;

        foreach ($this->commandsService->commandGroups as $group) {
            /** @var CommandGroupModel $group */
            $this->listGroup($group, $toCharacters);
        }

        $this->consoleService->writeLn('');
    }

    /**
     * List group
     * @param CommandGroupModel $group
     * @param int $toCharacters
     */
    private function listGroup(CommandGroupModel $group, $toCharacters = 2)
    {
        $this->consoleService->writeLn('');
        $this->consoleService->writeLn(lang('group:') . ' ', null, false);
        $this->consoleService->writeLn($group->name, 'yellow');

        foreach ($group->commands as $command) {
            /** @var CommandModel $command */
            $this->listCommand($command, $toCharacters);
        }
    }

    /**
     * List command
     * @param CommandModel $command
     * @param int $toCharacters
     */
    private function listCommand(CommandModel $command, $toCharacters)
    {
        $len = strlen($command->name);
        $to = abs($len - $toCharacters);

        $this->consoleService->writeLn('  ' . $command->name, 'green', false);

        if ($command->description) {
            $this->consoleService->writeLn(
                str_repeat(' ', $to) . $command->description,
                null,
                false
            );
        }

        $this->consoleService->writeLn('');
    }
}
