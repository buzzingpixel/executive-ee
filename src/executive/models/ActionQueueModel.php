<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\models;

use DateTime;

class ActionQueueModel
{
    public $id = 0;

    public $actionName = '';

    public $actionTitle = '';

    public $hasStarted = false;

    public $isFinished = false;

    public $percentComplete = 0;

    /** @var DateTime $addedAt */
    public $addedAt;

    /** @var DateTime $finishedAt */
    public $finishedAt;

    public $context = [];

    /** @var ActionQueueItemModel[] $items */
    public $items = [];
}
