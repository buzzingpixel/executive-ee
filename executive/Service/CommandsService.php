<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Service;

use BuzzingPixel\DataModel\ModelCollection;
use BuzzingPixel\Executive\BaseComponent;
use BuzzingPixel\DataModel\DataType;
use BuzzingPixel\Executive\Model\ArgumentsModel;
use BuzzingPixel\Executive\Model\CommandGroupModel;
use BuzzingPixel\Executive\Model\CommandModel;
use BuzzingPixel\Executive\Model\ScheduleModel;
use EllisLab\ExpressionEngine\Service\Addon\Factory as EEAddonFactory;
use EllisLab\ExpressionEngine\Service\Addon\Addon as EEAddon;
use BuzzingPixel\Executive\Service\ConsoleService;
use BuzzingPixel\Executive\Abstracts\BaseCommand;
use EllisLab\ExpressionEngine\Service\Database\Query as QueryBuilder;

/**
 * Class CommandsService
 *
 * @property-read ModelCollection $commandGroups
 * @property-read ModelCollection $schedule
 *
 * # Following properties for internal use only
 * @property-read EEAddonFactory $eeAddonFactory Internal use only
 * @property-read \EE_Config $eeConfigService
 * @property-read ConsoleService $consoleService
 * @property-read QueryBuilder $queryBuilder
 */
class CommandsService extends BaseComponent
{
    /** @var ModelCollection $commandGroupStorage */
    private $commandGroupStorage;

    /** @var ModelCollection $scheduleStorage */
    private $scheduleStorage;

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
            'queryBuilder' => array(
                'type' => DataType::INSTANCE,
                'expect' => '\EllisLab\ExpressionEngine\Service\Database\Query',
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

            /** @var array $addonCommands */
            $addonCommands = $provider->get('commands');
            $groupName = $provider->getPrefix();
            $commands = array();

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
     * Get schedule
     * @return ModelCollection
     */
    public function getSchedule()
    {
        if ($this->scheduleStorage !== null) {
            return $this->scheduleStorage;
        }

        $scheduleCollection = new ModelCollection();

        if ($userSchedule = $this->eeConfigService->item('schedule')) {
            /** @var array $userSchedule */

            foreach ($userSchedule as $schedule) {
                $schedule['arguments'] = isset($schedule['arguments']) ?
                    $schedule['arguments'] :
                    array();

                $model = new ScheduleModel($schedule);

                $model->source = 'user';

                $model->commandModel = $this->getCommand(
                    $model->group,
                    $model->command
                );

                $args = array(
                    $model->group,
                    $model->command,
                );

                foreach ($schedule['arguments'] as $key => $val) {
                    $args[] = "--{$key}={$val}";
                }

                $model->argumentsModel = new ArgumentsModel(array(
                    'rawArgs' => $args
                ));

                $model = $this->populateScheduleModelFromDb($model);

                $scheduleCollection->addModel($model);
            }
        }

        foreach ($this->eeAddonFactory->all() as $addon) {
            /** @var EEAddon $addon */

            $provider = $addon->getProvider();

            if (! $addon->isInstalled() || ! $provider->get('commands')) {
                continue;
            }

            /** @var array $addonSchedule */
            $addonSchedule = $provider->get('schedule') ?: array();

            foreach ($addonSchedule as $schedule) {
                $schedule['arguments'] = isset($schedule['arguments']) ?
                    $schedule['arguments'] :
                    array();

                $model = new ScheduleModel($schedule);

                $model->source = $provider->getPrefix();

                $model->commandModel = $this->getCommand(
                    $model->group,
                    $model->command
                );

                $args = array(
                    $model->group,
                    $model->command,
                );

                foreach ($schedule['arguments'] as $key => $val) {
                    $args[] = "--{$key}={$val}";
                }

                $model->argumentsModel = new ArgumentsModel(array(
                    'rawArgs' => $args
                ));

                $model = $this->populateScheduleModelFromDb($model);

                $scheduleCollection->addModel($model);
            }
        }

        $this->scheduleStorage = $scheduleCollection;

        return $this->scheduleStorage;
    }

    /**
     * Get schedule ID by name
     * @param ScheduleModel $scheduleModel
     * @return ScheduleModel
     */
    private function populateScheduleModelFromDb(ScheduleModel $scheduleModel)
    {
        $queryBuilder = clone $this->queryBuilder;

        $recordQuery = $queryBuilder->where('name', $scheduleModel->name)
            ->limit(1)
            ->get('executive_schedule_tracking');

        if ($recordQuery->num_rows() < 1) {
            $queryBuilder = clone $this->queryBuilder;

            $queryBuilder->insert('executive_schedule_tracking', array(
                'name' => $scheduleModel->name,
                'isRunning' => 0,
            ));

            $recordId = $this->queryBuilder->insert_id();

            $queryBuilder = clone $this->queryBuilder;

            $recordQuery = $queryBuilder->where('id', $recordId)
                ->limit(1)
                ->get('executive_schedule_tracking');
        }

        $record = $recordQuery->row();

        $scheduleModel->id = $record->id;
        $scheduleModel->isRunning = $record->isRunning;
        $scheduleModel->lastRunStartTime = $record->lastRunStartTime;
        $scheduleModel->lastRunEndTime = $record->lastRunEndTime;

        return $scheduleModel;
    }

    /**
     * Set schedule is running
     * @param ScheduleModel $scheduleModel
     */
    public function setScheduleIsRunning(ScheduleModel $scheduleModel)
    {
        $queryBuilder = clone $this->queryBuilder;

        $now = new \DateTime();

        $queryBuilder->update(
            'executive_schedule_tracking',
            array(
                'isRunning' => 1,
                'lastRunStartTime' => $now->format('Y-m-d H:i:s'),
            ),
            array(
                'id' => $scheduleModel->id,
            )
        );
    }

    /**
     * Set schedule is finished
     * @param ScheduleModel $scheduleModel
     */
    public function setScheduleFinished(ScheduleModel $scheduleModel)
    {
        $queryBuilder = clone $this->queryBuilder;

        $now = new \DateTime();

        $queryBuilder->update(
            'executive_schedule_tracking',
            array(
                'isRunning' => 0,
                'lastRunEndTime' => $now->format('Y-m-d H:i:s'),
            ),
            array(
                'id' => $scheduleModel->id,
            )
        );
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

        $class = new $class;

        if (! $class instanceof BaseCommand ||
            ! method_exists($class, $method)
        ) {
            $this->consoleService->writeLn(lang('classMethodNotFound'), 'red');
            return;
        }

        $rMethod = new \ReflectionMethod($class, $method);
        $rArguments = $rMethod->getParameters();

        $argsArray = array();

        foreach ($rArguments as $rArgument) {
            $argsArray[] = $argumentsModel->getArgument($rArgument->name);
        }

        try {
            call_user_func_array(
                array(
                    $class,
                    $method,
                ),
                $argsArray
            );
        } catch (\Exception $e) {
            $exceptionCaught = lang('followingExceptionCaught');
            $this->consoleService->writeLn("<bold>{$exceptionCaught}</bold>", 'red');
            $this->consoleService->writeLn($e->getMessage(), 'red');
            $this->consoleService->writeLn("File: {$e->getFile()}");
            $this->consoleService->writeLn("Line: {$e->getLine()}");
            if ($argumentsModel->getArgument('trace') !== 'true') {
                $this->consoleService->writeLn(lang('getTrace'), 'yellow');
                return;
            }
            print_r($e->getTrace());
        }
    }
}
