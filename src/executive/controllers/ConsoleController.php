<?php

declare(strict_types=1);

namespace buzzingpixel\executive\controllers;

use buzzingpixel\executive\exceptions\InvalidCommandException;
use buzzingpixel\executive\exceptions\InvalidCommandGroupException;
use buzzingpixel\executive\models\CliArgumentsModel;
use buzzingpixel\executive\models\CommandModel;
use buzzingpixel\executive\services\CommandsService;
use buzzingpixel\executive\services\RunCommandService;
use EE_Lang;
use ReflectionException;
use Symfony\Component\Console\Output\OutputInterface;
use const PHP_EOL;
use function abs;
use function mb_strlen;
use function str_repeat;

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
     */
    public function __construct(
        CliArgumentsModel $cliArgumentsModel,
        OutputInterface $consoleOutput,
        CommandsService $commandsService,
        EE_Lang $lang,
        RunCommandService $runCommandService
    ) {
        $this->cliArgumentsModel = $cliArgumentsModel;
        $this->consoleOutput     = $consoleOutput;
        $this->commandsService   = $commandsService;
        $this->lang              = $lang;
        $this->runCommandService = $runCommandService;
    }

    /**
     * Runs the console controller
     *
     * @throws ReflectionException
     * @throws InvalidCommandException
     * @throws InvalidCommandGroupException
     */
    public function run() : void
    {
        $groups = $this->commandsService->getCommandGroups();

        $group = $this->cliArgumentsModel->getArgument('group');

        if ($group === null) {
            $this->listCommands($groups);

            return;
        }

        $groupModels = $groups[$group] ?? null;

        if (! $groupModels) {
            throw new InvalidCommandGroupException(
                $this->lang->line('groupNotFound') . ': ' . $group
            );
        }

        $command = $this->cliArgumentsModel->getArgument('command');

        if ($command === null) {
            $this->listCommands([$group => $groupModels]);

            return;
        }

        $commandModel = $groupModels[$command] ?? null;

        if (! $commandModel) {
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
     */
    private function listCommands(array $groups) : void
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
                $len          = mb_strlen($commandModel->getName());
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

                $len = mb_strlen($commandModel->getName());
                $to  = abs($len - $toCharacters);

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
