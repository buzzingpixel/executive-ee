<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use buzzingpixel\executive\models\ScheduleItemModel;
use buzzingpixel\executive\services\RunCommandService;
use buzzingpixel\executive\services\ScheduleService;
use EE_Lang;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function array_map;
use function count;
use function in_array;
use function is_array;

class RunScheduleCommand
{
    /** @var EE_Lang $lang */
    private $lang;
    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;
    /** @var ScheduleService $scheduleService */
    private $scheduleService;
    /** @var RunCommandService $runCommandService */
    private $runCommandService;

    /**
     * RunScheduleCommand constructor
     */
    public function __construct(
        EE_Lang $lang,
        OutputInterface $consoleOutput,
        ScheduleService $scheduleService,
        RunCommandService $runCommandService
    ) {
        $this->consoleOutput     = $consoleOutput;
        $this->lang              = $lang;
        $this->scheduleService   = $scheduleService;
        $this->runCommandService = $runCommandService;
    }

    /**
     * Run schedule
     *
     * @throws Throwable
     */
    public function run() : void
    {
        $schedule = $this->scheduleService->getSchedule();

        if (count($schedule) < 1) {
            $this->consoleOutput->writeln('<fg=yellow>' . $this->lang->line('noScheduledCommands') . '</>');

            return;
        }

        array_map([$this, 'runScheduleItem'], $schedule);
    }

    /**
     * Runs a schedule item
     *
     * @throws Throwable
     */
    public function runScheduleItem(ScheduleItemModel $model) : void
    {
        try {
            $this->runScheduleItemInner($model);
        } catch (Throwable $e) {
            $model->setRunning(0);

            $this->scheduleService->saveSchedule($model);

            $args = EXECUTIVE_RAW_ARGS;
            $args = is_array($args) ? $args : [];

            if (in_array('--trace=true', $args, true)) {
                throw $e;
            }

            $this->consoleOutput->writeln(
                '<fg=red>There was a problem running a scheduled command.</>'
            );
            $this->consoleOutput->writeln(
                '<fg=red>Name (groupThatScheduled/runGroup/CommandName): ' .
                $model->getName() .
                '</>'
            );
            $this->consoleOutput->writeln(
                '<fg=red>Message: ' .
                $e->getMessage() .
                '</>'
            );
            $this->consoleOutput->writeln(
                '<fg=yellow>' .
                $this->lang->line('getTrace') .
                '</>'
            );
        }
    }

    /**
     * Runs a schedule item
     *
     * @throws Throwable
     */
    public function runScheduleItemInner(ScheduleItemModel $model) : void
    {
        if (! $model->shouldRun() && $model->isRunning()) {
            $this->consoleOutput->writeln(
                '<fg=yellow>' .
                $model->getName() .
                ' ' .
                $this->lang->line('isCurrentlyRunning') .
                '</>'
            );

            return;
        }

        if (! $model->shouldRun()) {
            $this->consoleOutput->writeln(
                '<fg=green>' .
                $model->getName() .
                ' ' .
                $this->lang->line('notRunYet') .
                '</>'
            );

            return;
        }

        $this->consoleOutput->writeln(
            '<fg=yellow>' .
            $this->lang->line('runningCommand') .
            ': ' .
            $model->getName() .
            '</> '
        );

        $model->setRunning(1);

        $model->setLastRunStartTime('now');

        $this->scheduleService->saveSchedule($model);

        $this->runCommandService->runCommand($model->getCommandModel());

        $model->setRunning(0);

        $model->setLastRunEndTime('now');

        $this->scheduleService->saveSchedule($model);

        $this->consoleOutput->writeln(
            '<fg=green>' .
            $model->getName() .
            ' ' .
            $this->lang->line('ranSuccessfully') .
            '</>'
        );
    }
}
