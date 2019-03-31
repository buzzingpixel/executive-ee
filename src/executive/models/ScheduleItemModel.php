<?php

declare(strict_types=1);

namespace buzzingpixel\executive\models;

use buzzingpixel\executive\abstracts\ModelAbstract;
use buzzingpixel\executive\traits\DateTimeTrait;
use buzzingpixel\executive\traits\TruthyTrait;
use DateTime;
use function is_numeric;
use function mb_strtolower;

class ScheduleItemModel extends ModelAbstract
{
    use TruthyTrait;
    use DateTimeTrait;

    public const RUN_EVERY_MAP = [
        'always' => 0,
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
    ];

    public const MIDNIGHT_STRINGS = [
        'dayatmidnight',
        'saturdayatmidnight',
        'sundayatmidnight',
        'mondayatmidnight',
        'tuesdayatmidnight',
        'wednesdayatmidnight',
        'thursdayatmidnight',
        'fridayatmidnight',
    ];

    /** @var int $id */
    private $id = 0;

    /**
     * Sets the ID. Incoming value will be type coerced to integer
     *
     * @return ScheduleItemModel
     */
    public function setId(int $val) : self
    {
        $this->id = (int) $val;

        return $this;
    }

    /**
     * Gets the ID
     */
    public function getId() : int
    {
        return $this->id;
    }

    /** @var bool $running */
    private $running = false;

    /**
     * Sets Running. Strings of `"true"` or `"false"` will be converted to
     * boolean value `true` or `false` respectively. Additionally `"1"` and
     * `"0"` will be treated the same way. Anything that does not equal a
     * "truthy" value will be considered false
     *
     * @param mixed $val
     */
    public function setRunning($val) : self
    {
        $this->running = $this->isValueTruthy($val);

        return $this;
    }

    /**
     * Checks if schedule item is running
     */
    public function isRunning() : bool
    {
        return $this->running;
    }

    /** @var DateTime $lastRunStartTime */
    private $lastRunStartTime;

    /**
     * Sets Last Run Start Time. Incoming value will be converted to a DateTime
     * object if it is not already.
     *
     * @return ScheduleItemModel
     */
    public function setLastRunStartTime($val) : self
    {
        $this->lastRunStartTime = $this->createDateTimeFromVal($val);

        return $this;
    }

    /**
     * Gets Last Run Start Time
     */
    public function getLastRunStartTime() : DateTime
    {
        return $this->createDateTimeFromVal($this->lastRunStartTime);
    }

    /** @var DateTime $lastRunEndTime */
    private $lastRunEndTime;

    /**
     * Sets Last Run End Time. Incoming value will be converted to a DateTime
     * object if it is not already.
     *
     * @return ScheduleItemModel
     */
    public function setLastRunEndTime($val) : self
    {
        $this->lastRunEndTime = $this->createDateTimeFromVal($val);

        return $this;
    }

    /**
     * Gets Last Run End Time
     */
    public function getLastRunEndTime() : DateTime
    {
        return $this->createDateTimeFromVal($this->lastRunEndTime);
    }

    /** @var string $source */
    private $source = '';

    /**
     * Sets Source. Incoming value will be type coerced to string
     *
     * @return ScheduleItemModel
     */
    public function setSource(string $val) : self
    {
        $this->source = $val;

        return $this;
    }

    /**
     * Gets Source
     */
    public function getSource() : string
    {
        return $this->source;
    }

    /** @var string $group */
    private $group = '';

    /**
     * Sets Group. Incoming value will be type coerced to string
     *
     * @return ScheduleItemModel
     */
    public function setGroup(string $val) : self
    {
        $this->group = $val;

        return $this;
    }

    /**
     * Gets Group
     */
    public function getGroup() : string
    {
        return $this->group;
    }

    /** @var string $command */
    private $command = '';

    /**
     * Sets Command. Incoming value will be type coerced to string
     *
     * @return ScheduleItemModel
     */
    public function setCommand(string $val) : self
    {
        $this->command = $val;

        return $this;
    }

    /**
     * Gets Command
     */
    public function getCommand() : string
    {
        return $this->command;
    }

    /** @var string $runEvery */
    private $runEvery = '';

    /**
     * Sets RunEvery. Incoming value will be type coerced to string
     *
     * @return ScheduleItemModel
     */
    public function setRunEvery(string $val) : self
    {
        $this->runEvery = $val;

        return $this;
    }

    /**
     * Gets RunEvery
     */
    public function getRunEvery() : string
    {
        return $this->runEvery;
    }

    /** @var CommandModel $commandModel */
    private $commandModel = '';

    /**
     * Sets Command Model. Incoming value will be type coerced to string
     *
     * @return ScheduleItemModel
     */
    public function setCommandModel(CommandModel $val) : self
    {
        $this->commandModel = $val;

        return $this;
    }

    /**
     * Gets Command Model
     */
    public function getCommandModel() : CommandModel
    {
        return $this->commandModel;
    }

    /**
     * Composes the name from the various properties and returns it
     */
    public function getName() : string
    {
        return $this->getSource() .
            '/' .
            $this->getGroup() .
            '/' .
            $this->getCommand();
    }

    /**
     * Translates run every into actionable values.
     * - If the value of runEvery is numeric, it is assumed to be minutes and
     * will be converted to seconds
     * - Else if the runEvery value is not set on the RUN_EVERY_MAP, a 0 will be
     * returned (same value as always)
     * - Else if the runEvery mapped value is numeric, it is minutes and will be
     * converted to seconds and returned
     * - Else the mapped value will be returned
     *
     * @return mixed
     */
    public function getTranslatedRunEvery()
    {
        $val = $this->getRunEvery();

        if (is_numeric($val)) {
            return ((int) $val) * 60;
        }

        $val = mb_strtolower($val);

        if (! isset(self::RUN_EVERY_MAP[$val])) {
            return 0;
        }

        $mappedVal = self::RUN_EVERY_MAP[$val];

        if (is_numeric($mappedVal)) {
            $mappedVal = (int) $mappedVal;

            return $mappedVal * 60;
        }

        return $mappedVal;
    }

    /**
     * Checks whether it's time for the schedule to run
     */
    public function shouldRun() : bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $currentTime         = new DateTime();
        $currentTimeStamp    = $currentTime->getTimestamp();
        $lastRunTimeStamp    = $this->getLastRunStartTime()->getTimestamp();
        $oneHourInSeconds    = 60 * 60;
        $secondsSinceLastRun = $currentTimeStamp - $lastRunTimeStamp;
        $runEvery            = $this->getTranslatedRunEvery();

        // If the task is running, wait on hour before trying again
        if ($secondsSinceLastRun < $oneHourInSeconds && $this->isRunning()) {
            return false;
        }

        // If $runEvery is numeric we'll check if it's time to run based on that
        if (is_numeric($runEvery)) {
            $runEvery = (int) $runEvery;

            return $secondsSinceLastRun >= $runEvery;
        }

        /**
         * Now we know it's a midnight string and we're checking for that
         */

        // Increment timestamp by 20 hours
        $incrementTime = $lastRunTimeStamp + 72000;

        /**
         * Don't run if it hasn't been more than 20 hours (we're trying to
         * hit the right window, but we can't be too precise because what if
         * the cron doesn't run right at midnight. But we also only want to
         * run this once)
         */
        if ($incrementTime > $currentTimeStamp) {
            return false;
        }

        // If the hour is not in the midnight range, we know we can stop
        if ($currentTime->format('H') !== '00') {
            return false;
        }

        // Now if we're running every day, we know it's time to run
        if ($runEvery === 'dayatmidnight') {
            return true;
        }

        $day = $currentTime->format('l');

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
        return $runEvery === 'fridayatmidnight' && $day === 'Friday';
    }
}
