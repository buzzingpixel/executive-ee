<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\models\ScheduleItemModel;

class ScheduleItemModelFactory
{
    /**
     * Gets a ScheduleItemModel instance
     */
    public function make() : ScheduleItemModel
    {
        return new ScheduleItemModel();
    }
}
