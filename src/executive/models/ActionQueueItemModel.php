<?php

declare(strict_types=1);

namespace buzzingpixel\executive\models;

use DateTime;

class ActionQueueItemModel
{
    /** @var int $id */
    public $id = 0;
    /** @var bool $isFinished */
    public $isFinished = false;
    /** @var DateTime $finishedAt */
    public $finishedAt;
    /** @var string $class */
    public $class = '';
    /** @var string $method */
    public $method = '';
    /** @var array $context */
    public $context = [];
}
