<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\controllers;

use EE_Lang;
use ReflectionException;
use buzzingpixel\executive\models\CommandModel;
use buzzingpixel\executive\services\CommandsService;
use Symfony\Component\Console\Output\OutputInterface;
use buzzingpixel\executive\services\RunCommandService;
use buzzingpixel\executive\models\CliArgumentsModel;
use buzzingpixel\executive\exceptions\InvalidCommandException;
use buzzingpixel\executive\exceptions\InvalidCommandGroupException;

/**
 * Class ConsoleController
 */
class ConsoleController
{
    /** @var CliArgumentsModel $cliArgumentsModel */
    private $cliArgumentsModel;

    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;

    /** @var CommandsService $commandsService */
    private $commandsService;

    /** @var EE_Lang $lang */
    private $lang;

    /** @var RunCommandService $runCommandService */
    private $runCommandService;

    /**
     * ConsoleController constructor
     * @param CliArgumentsModel $cliArgumentsModel
     * @param OutputInterface $consoleOutput
     * @param CommandsService $commandsService
     * @param EE_Lang $lang
     * @param RunCommandService $runCommandService
     */
    public function __construct(
        CliArgumentsModel $cliArgumentsModel,
        OutputInterface $consoleOutput,
        CommandsService $commandsService,
        EE_Lang $lang,
        RunCommandService $runCommandService
    ) {
        $this->cliArgumentsModel = $cliArgumentsModel;
        $this->consoleOutput = $consoleOutput;
        $this->commandsService = $commandsService;
        $this->lang = $lang;
        $this->runCommandService = $runCommandService;
    }

    /**
     * Runs the console controller
     * @throws ReflectionException
     * @throws InvalidCommandException
     * @throws InvalidCommandGroupException
     */
    public function run(): void
    {
        $groups = $this->commandsService->getCommandGroups();

        $group = $this->cliArgumentsModel->getArgument('group');

        if ($group === null) {
            $this->listCommands($groups);
            return;
        }

        if (! $groupModels = $groups[$group] ?? null) {
            throw new InvalidCommandGroupException(
                $this->lang->line('groupNotFound') . ': ' . $group
            );
        }

        $command = $this->cliArgumentsModel->getArgument('command');

        if ($command === null) {
            $this->listCommands([$group => $groupModels]);
            return;
        }

        if (! $commandModel = $groupModels[$command] ?? null) {
            throw new InvalidCommandException(
                $this->lang->line('commandNotFound') .
                ': ' .
                $group .
                ' ' .
                $command
            );
        }

        $this->runCommandService->runCommand($commandModel);
    }

    /**
     * Lists commands
     * @param array $groups
     */
    private function listCommands(array $groups): void
    {
        $this->consoleOutput->writeln(
            $this->lang->line('executiveCommandLine') .
            ' ' .
            '<fg=green>' .
            EXECUTIVE_VER .
            '</>' .
            PHP_EOL
        );

        $this->consoleOutput->writeln(
            '<fg=yellow>' .
            $this->lang->line('usage:') .
            '</>'
        );

        $this->consoleOutput->writeln(
            '  ' . $this->lang->line('usageExample') . PHP_EOL
        );

        $toCharacters = 0;

        foreach ($groups as $group) {
            foreach ($group as $commandModel) {
                /** @var CommandModel $commandModel */
                $len = \strlen($commandModel->getName());
                $toCharacters = $len > $toCharacters ? $len : $toCharacters;
            }
        }

        $toCharacters += 2;

        foreach ($groups as $groupName => $group) {
            $this->consoleOutput->writeln(
                $this->lang->line('group:') .
                ' ' .
                '<fg=yellow>' .
                $groupName .
                '</>'
            );

            foreach ($group as $commandModel) {
                /** @var CommandModel $commandModel */

                $len = \strlen($commandModel->getName());
                $to = abs($len - $toCharacters);

                $this->consoleOutput->writeln(
                    '<fg=green>  ' .
                    $commandModel->getName() .
                    str_repeat(' ', $to) .
                    '</>' .
                    $commandModel->getDescription()
                );
            }

            $this->consoleOutput->writeln('');
        }
    }
}
