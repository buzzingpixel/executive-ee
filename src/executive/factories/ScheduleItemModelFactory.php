<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\models\ScheduleItemModel;

/**
 * Class ScheduleItemModelFactory
 */
class ScheduleItemModelFactory
{
    /**
     * Gets a ScheduleItemModel instance
     * @return ScheduleItemModel
     */
    public function make(): ScheduleItemModel
    {
        return new ScheduleItemModel();
    }
}
