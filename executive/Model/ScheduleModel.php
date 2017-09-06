<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
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
        'dayatmidnight' => 'dayatmidnight',
        'saturdayatmidnight' => 'saturdayatmidnight',
        'sundayatmidnight' => 'sundayatmidnight',
        'mondayatmidnight' => 'mondayatmidnight',
        'tuesdayatmidnight' => 'tuesdayatmidnight',
        'wednesdayatmidnight' => 'wednesdayatmidnight',
        'thursdayatmidnight' => 'thursdayatmidnight',
        'fridayatmidnight' => 'fridayatmidnight',
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

        $mappedVal = $this->shouldRunMap[$val];

        if (is_string($mappedVal)) {
            return $mappedVal;
        }

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
        $runEvery = $this->runEvery;

        // If the task is running, wait an hour before trying again
        if ($this->isRunning && $secondsSinceLastRun < $oneHourInSeconds) {
            return false;
        }

        // If $runEvery is an integer, we can calculate minutes
        if (is_int($runEvery)) {
            return $secondsSinceLastRun >= $runEvery;
        }

        /**
         * Now we're checking for our midnight strings
         */

        // Increment timestamp by 20 hours
        $incrementTime = $lastRunTimestamp + 72000;

        // Check if it's been more than 20 hours
        if ($incrementTime > time()) {
            return false;
        }

        // Get config service
        /** @var \EE_Config $configService */
        $configService = ee()->config;

        // Get the timezone
        $timezone = $configService->item('default_site_timezone') ?: 'UTC';

        // Get current time with correct timezone
        $time = new \DateTime('now', new \DateTimeZone($timezone));

        // If it is not midnight, we know we can stop now
        if ($time->format('H') !== '00') {
            return false;
        }

        $day = $time->format('l');

        // If we're running every day, we know it's time to run now
        if ($runEvery === 'dayatmidnight') {
            return true;
        }

        // If we're running on Saturday, and it's Saturyday, we should run
        if ($runEvery === 'saturdayatmidnight' && $day === 'Saturday') {
            return true;
        }

        // If we're running on Sunday, and it's Sunday, we should run
        if ($runEvery === 'sundayatmidnight' && $day === 'Sunday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        if ($runEvery === 'mondayatmidnight' && $day === 'Monday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        if ($runEvery === 'tuesdayatmidnight' && $day === 'Tuesday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        if ($runEvery === 'wednesdayatmidnight' && $day === 'Wednesday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        if ($runEvery === 'thursdayatmidnight' && $day === 'Thursday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        if ($runEvery === 'fridayatmidnight' && $day === 'Friday') {
            return true;
        }

        // We didn't have any matches and we shouldn't run
        return false;
    }
}
