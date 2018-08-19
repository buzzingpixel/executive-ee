<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Command;

use buzzingpixel\executive\abstracts\BaseCommand;
use BuzzingPixel\Executive\Model\ScheduleModel;
use BuzzingPixel\Executive\Service\CommandsService;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;

/**
 * Class ScheduleCommand
 */
class ScheduleCommand extends BaseCommand
{
    /** @var CommandsService $commandsService */
    private $commandsService;

    /** @var QueryBuilder $queryBuilder */
    protected $queryBuilder;

    /**
     * Initialize
     */
    public function initCommand()
    {
        $this->commandsService = ee('executive:CommandsService');
        $this->queryBuilder = ee('db');
    }

    /**
     * Run schedule
     */
    public function run()
    {
        if (count($this->commandsService->schedule) < 1) {
            $this->consoleService->writeLn(
                lang('noScheduledCommands'),
                'yellow'
            );
            return;
        }

        // Let's try not to run out of time
        @set_time_limit(0);

        foreach ($this->commandsService->schedule as $scheduleModel) {
            /** @var ScheduleModel $scheduleModel */
            $this->runScheduleItem($scheduleModel);
        }
    }

    /**
     * Run schedule item
     * @param ScheduleModel $scheduleModel
     */
    private function runScheduleItem(ScheduleModel $scheduleModel)
    {
        if ($scheduleModel->commandModel === null) {
            $this->consoleService->writeLn(
                lang('commandNotFound') . ': ' .
                    $scheduleModel->group . ' ' .
                    $scheduleModel->command,
                'red'
            );
            return;
        }

        if (! $scheduleModel->shouldRun) {
            if ($scheduleModel->isRunning) {
                $this->consoleService->writeLn(
                    "\"{$scheduleModel->name}\" " . lang('isCurrentlyRunning'),
                    'yellow'
                );
                return;
            }

            if (! $scheduleModel->isRunning) {
                $this->consoleService->writeLn(
                    "\"{$scheduleModel->name}\" " . lang('notRunYet'),
                    'green'
                );
                return;
            }

            return;
        }

        $success = true;

        $this->commandsService->setScheduleIsRunning($scheduleModel);

        try {
            $this->commandsService->runCommand(
                $scheduleModel->commandModel,
                $scheduleModel->argumentsModel
            );
        } catch (\Exception $e) {
            $success = false;
            $this->consoleService->writeLn(
                lang('anErrorOccurredRunningCommand:'),
                'red'
            );
            $this->consoleService->writeLn('  ' . $e->getMessage(), 'red');
        }

        $this->commandsService->setScheduleFinished($scheduleModel);

        if (! $success) {
            return;
        }

        $this->consoleService->writeLn(
            "\"{$scheduleModel->name}\" " . lang('ranSuccessfully'),
            'green'
        );
    }
}
