<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Controller;

use BuzzingPixel\Executive\BaseComponent;
use BuzzingPixel\Executive\Model\CommandGroupModel;
use BuzzingPixel\Executive\Model\CommandModel;
use BuzzingPixel\Executive\Service\ArgsService;
use BuzzingPixel\Executive\Model\ArgumentsModel;
use BuzzingPixel\Executive\Service\CommandsService;
use BuzzingPixel\Executive\Service\ConsoleService;
use EllisLab\ExpressionEngine\Service\Addon\Factory as AddonFactory;
use EllisLab\ExpressionEngine\Service\Addon\Addon;

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
     */
    public function runConsoleRequest()
    {
        if ($this->args->getArgument('group') === null) {
            $this->listCommands();
            return;
        }

        if ($this->args->getArgument('command') === null) {
            $this->consoleService->writeLn(lang('mustSpecifyCommand'), 'red');
            return;
        }

        var_dump('TODO:');
        var_dump($this->args->getArgument('addon'));
        die;
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

        $commandGroups = $this->commandsService->commandGroups;

        $toCharacters = 0;

        foreach ($commandGroups as $group) {
            /** @var CommandGroupModel $group */
            foreach ($group->commands as $command) {
                /** @var CommandModel $command */
                $len = strlen($command->name);
                $toCharacters = $len > $toCharacters ? $len : $toCharacters;
            }
        }

        $toCharacters += 2;

        foreach ($commandGroups as $group) {
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
