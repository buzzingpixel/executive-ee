<?php

declare(strict_types=1);

namespace buzzingpixel\executive\models;

use DateTime;

class ActionQueueModel
{
    /** @var int $id */
    public $id = 0;
    /** @var string $actionName */
    public $actionName = '';
    /** @var string $actionTitle */
    public $actionTitle = '';
    /** @var bool $hasStarted */
    public $hasStarted = false;
    /** @var bool $isFinished */
    public $isFinished = false;
    /** @var int $percentComplete */
    public $percentComplete = 0;
    /** @var DateTime $addedAt */
    public $addedAt;
    /** @var DateTime $finishedAt */
    public $finishedAt;
    /** @var array $context */
    public $context = [];
    /** @var ActionQueueItemModel[] $items */
    public $items = [];
}
