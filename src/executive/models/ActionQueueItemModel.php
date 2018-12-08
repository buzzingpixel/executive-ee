<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\models;

use DateTime;

class ActionQueueItemModel
{
    public $id = 0;

    public $isFinished = false;

    /** @var DateTime $finishedAt */
    public $finishedAt;

    public $class = '';

    public $method = '';

    public $context = [];
}
