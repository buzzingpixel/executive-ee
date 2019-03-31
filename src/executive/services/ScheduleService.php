<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\factories\CliArgumentsModelFactory;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\factories\ScheduleItemModelFactory;
use buzzingpixel\executive\models\CommandModel;
use buzzingpixel\executive\models\ScheduleItemModel;
use EE_Config;
use EllisLab\ExpressionEngine\Service\Addon\Addon;
use EllisLab\ExpressionEngine\Service\Addon\Factory as AddOnFactory;
use function array_filter;
use function array_map;
use function array_merge;
use function array_values;
use function is_array;

class ScheduleService
{
    /** @var EE_Config $config */
    private $config;
    /** @var AddOnFactory $addOnFactory */
    private $addOnFactory;
    /** @var CommandsService $commandsService */
    private $commandsService;
    /** @var ScheduleItemModelFactory $scheduleItemModelFactory */
    private $scheduleItemModelFactory;
    /** @var QueryBuilderFactory $queryBuilderFactory */
    private $queryBuilderFactory;
    /** @var CliArgumentsModelFactory $cliArgumentsModelFactory */
    private $cliArgumentsModelFactory;

    /**
     * ScheduleService constructor
     */
    public function __construct(
        EE_Config $config,
        AddOnFactory $addOnFactory,
        CommandsService $commandsService,
        ScheduleItemModelFactory $scheduleItemModelFactory,
        QueryBuilderFactory $queryBuilderFactory,
        CliArgumentsModelFactory $cliArgumentsModelFactory
    ) {
        $this->config                   = $config;
        $this->addOnFactory             = $addOnFactory;
        $this->commandsService          = $commandsService;
        $this->scheduleItemModelFactory = $scheduleItemModelFactory;
        $this->queryBuilderFactory      = $queryBuilderFactory;
        $this->cliArgumentsModelFactory = $cliArgumentsModelFactory;
    }

    /**
     * Gets the schedule item models
     *
     * @return ScheduleItemModel[]
     */
    public function getSchedule() : array
    {
        $scheduleConfig = [];
        $userSchedule   = $this->config->item('schedule');
        $userSchedule   = is_array($userSchedule) ? $userSchedule : [];

        if ($userSchedule) {
            $scheduleConfig['user'] = $userSchedule;
        }

        $providerSchedule = array_map(
            static function (Addon $addOn) {
                $provider = $addOn->getProvider();
                $schedule = $provider->get('schedule');
                $schedule = is_array($schedule) ? $schedule : [];

                if (! $schedule) {
                    return false;
                }

                return $schedule;
            },
            $this->addOnFactory->installed()
        );

        $providerSchedule = array_filter($providerSchedule, static function ($i) {
            return $i !== false;
        });

        $scheduleConfig = array_merge($scheduleConfig, $providerSchedule);

        $commandGroups = $this->commandsService->getCommandGroups();

        $namesToQuery   = [];
        $scheduleModels = [];

        foreach ($scheduleConfig as $sourceName => $config) {
            foreach ($config as $scheduleConfigItem) {
                $groupName = $scheduleConfigItem['group'] ?? '';
                $command   = $scheduleConfigItem['command'] ?? '';
                $group     = $commandGroups[$groupName] ?? '';

                /** @var CommandModel $commandModel */
                $commandModel = $group[$command] ?? null;

                if (! $commandModel) {
                    continue;
                }

                $arguments = [
                    'ee',
                    $groupName,
                    $command,
                ];

                foreach ($scheduleConfigItem['arguments'] ?? [] as $key => $val) {
                    $arguments[] = '--' . $key . '=' . $val;
                }

                $commandModel->setCustomCliArgumentsModel(
                    $this->cliArgumentsModelFactory->make($arguments)
                );

                $model = $this->scheduleItemModelFactory->make();

                $model->setSource($sourceName);
                $model->setGroup($groupName);
                $model->setCommand($command);
                $model->setRunEvery($scheduleConfigItem['runEvery'] ?? 'Always');
                $model->setCommandModel($commandModel);

                $namesToQuery[]                    = $model->getName();
                $scheduleModels[$model->getName()] = $model;
            }
        }

        if ($namesToQuery) {
            $query = $this->queryBuilderFactory->make()
                ->where_in('name', $namesToQuery)
                ->get('executive_schedule_tracking')
                ->result();

            foreach ($query as $item) {
                /** @var ScheduleItemModel $model */
                $model = $scheduleModels[$item->name] ?? null;

                if (! $model) {
                    continue;
                }

                $model->setId($item->id);
                $model->setRunning($item->isRunning);
                $model->setLastRunStartTime($item->lastRunStartTime);
                $model->setLastRunEndTime($item->lastRunEndTime);
            }
        }

        return array_values($scheduleModels);
    }

    /**
     * Saves the schedule model
     */
    public function saveSchedule(ScheduleItemModel $model) : void
    {
        $saveArray = [
            'name' => $model->getName(),
            'isRunning' => $model->isRunning() ? '1' : '0',
            'lastRunStartTime' => $model->getLastRunStartTime()->format(
                $model->getDatabaseFormatString()
            ),
            'lastRunEndTime' => $model->getLastRunEndTime()->format(
                $model->getDatabaseFormatString()
            ),
        ];

        $id = $model->getId();

        $queryBuilder = $this->queryBuilderFactory->make();

        if ($id) {
            $queryBuilder->update(
                'executive_schedule_tracking',
                $saveArray,
                ['id' => $id]
            );

            return;
        }

        $queryBuilder->insert(
            'executive_schedule_tracking',
            $saveArray
        );

        $model->setId($queryBuilder->insert_id());
    }
}
