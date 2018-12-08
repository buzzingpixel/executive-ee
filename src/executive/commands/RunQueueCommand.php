<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\commands;

use Throwable;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\QueueApi;
use buzzingpixel\executive\models\ActionQueueItemModel;

class RunQueueCommand
{
    private $di;
    private $queueApi;

    public function __construct(ExecutiveDi $di, QueueApi $queueApi)
    {
        $this->di = $di;
        $this->queueApi = $queueApi;
    }

    public function run(): ?int
    {
        $item = $this->queueApi->getNextQueueItem(true);

        if (! $item) {
            return null;
        }

        try {
            return $this->runInner($item);
        } catch (Throwable $e) {
            $this->queueApi->markAsStoppedDueToError($item);
            return 1;
        }
    }

    /**
     * @throws Throwable
     */
    public function runInner(ActionQueueItemModel $item): ?int
    {
        $constructedClass = null;

        if ($this->di->hasDefinition($item->class)) {
            $constructedClass = $this->di->makeFromDefinition($item->class);
        }

        if (! $constructedClass) {
            $constructedClass = new $item->class;
        }

        $constructedClass->{$item->method}($item->context);

        $this->queueApi->markItemAsRun($item);

        return null;
    }
}
