<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license https://buzzingpixel.com/software/executive/license
 * @link https://buzzingpixel.com/software/executive
 */

namespace BuzzingPixel\Executive\Model;

use BuzzingPixel\DataModel\Model as DataModel;
use BuzzingPixel\DataModel\DataType;

/**
 * Class ScheduleModel
 * @property int $id
 * @property-read string $name
 * @property bool $isRunning
 * @property \DateTime $lastRunStartTime
 * @property \DateTime $lastRunEndTime
 * @property string $source
 * @property string $group
 * @property string $command
 * @property int $runEvery
 * @property CommandModel $commandModel
 * @property ArgumentsModel $argumentsModel
 * @property-read bool $shouldRun
 */
class ScheduleModel extends DataModel
{
    /**
     * Define attributes
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'id' => DataType::INT,
            'isRunning' => DataType::BOOL,
            'lastRunStartTime' => DataType::DATETIME,
            'lastRunEndTime' => DataType::DATETIME,
            'source' => DataType::STRING,
            'group' => DataType::STRING,
            'command' => DataType::STRING,
            'runEvery' => DataType::MIXED,
            'commandModel' => array(
                'type' => DataType::INSTANCE,
                'expect' => '\BuzzingPixel\Executive\Model\CommandModel',
            ),
            'argumentsModel' => array(
                'type' => DataType::INSTANCE,
                'expect' => '\BuzzingPixel\Executive\Model\ArgumentsModel',
            ),
        );
    }

    /**
     * Get name
     */
    public function getName()
    {
        return "{$this->source}/{$this->group}/{$this->command}";
    }

    /** @var array $shouldRunMap */
    private $shouldRunMap = array(
        'fiveminutes' => 5,
        'tenminutes' => 10,
        'thirtyminutes' => 30,
        'hour' => 60,
        'day' => 1440,
        'week' => 10080,
        'month' => 43800,
    );

    /**
     * Get run every
     * @param mixed $val
     * @return int Seconds interval
     */
    protected function getRunEvery($val)
    {
        if (is_numeric($val)) {
            return ((int) $val) * 60;
        }

        $val = strtolower($val);

        return isset($this->shouldRunMap[$val]) ?
            $this->shouldRunMap[$val] * 60 :
            1;
    }

    /**
     * Get should run
     * @return bool
     */
    public function getShouldRun()
    {
        $currentTime = new \DateTime();
        $currentTimeStamp = $currentTime->getTimestamp();
        $lastRunTimestamp = $this->lastRunStartTime->getTimestamp();
        $oneHourInSeconds = 60 * 60;
        $secondsSinceLastRun = $currentTimeStamp - $lastRunTimestamp;

        // If the task is running, wait an hour before trying again
        if ($this->isRunning && $secondsSinceLastRun < $oneHourInSeconds) {
            return false;
        }

        return $secondsSinceLastRun >= $this->runEvery;
    }
}
