<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Controller;

use BuzzingPixel\Executive\BaseComponent;
use BuzzingPixel\Executive\Service\ArgsService;
use BuzzingPixel\Executive\Model\ArgumentsModel;
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

    /** @var \EE_Config $eeConfig */
    private $eeConfig;

    /** @var AddonFactory $addonFactory */
    private $addonFactory;

    /**
     * Init
     */
    public function init()
    {
        /** @var ArgsService $argsService */
        $argsService = ee('executive:ArgsService');

        $this->args = $argsService->parseRawArgs(EXECUTIVE_RAW_ARGS);
        $this->consoleService = ee('executive:ConsoleService');
        $this->eeConfig = ee()->config;
        $this->addonFactory = ee('Addon');
    }

    /**
     * Run console request
     */
    public function runConsoleRequest()
    {
        if ($this->args->getArgument('addon') === null) {
            $this->listCommands();
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

        // Put together an array of command groups
        $commandGroups = array();

        // Get user commands
        $userCommands = $this->eeConfig->item('commands') ?: array();

        if ($userCommands) {
            $commandGroups['user'] = $userCommands;
        }

        foreach ($this->addonFactory->all() as $addon) {
            /** @var Addon $addon */

            $provider = $addon->getProvider();

            if (! $addon->isInstalled() || ! $provider->get('commands')) {
                continue;
            }

            $commandGroups[$provider->getPrefix()] = $provider->get('commands');
        }

        $toCharacters = 0;

        foreach ($commandGroups as $group => $commands) {
            foreach (array_keys($commands) as $command) {
                $len = strlen($command);
                if ($len > $toCharacters) {
                    $toCharacters = $len;
                }
            }
        }

        $toCharacters += 2;

        foreach ($commandGroups as $group => $commands) {
            $this->consoleService->writeLn('');
            $this->consoleService->writeLn(lang('group:') . ' ', null, false);
            $this->consoleService->writeLn($group, 'yellow');

            foreach ($commands as $command => $details) {
                $len = strlen($command);
                $to = abs($len - $toCharacters);

                $this->consoleService->writeLn('  ' . $command, 'green', false);

                if (isset($details['description'])) {
                    $this->consoleService->writeLn(
                        str_repeat(' ', $to) . $details['description'],
                        null,
                        false
                    );
                }

                $this->consoleService->writeLn('');
            }
        }

        $this->consoleService->writeLn('');
    }
}
