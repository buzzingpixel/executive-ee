<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\models\ActionQueueItemModel;
use buzzingpixel\executive\services\QueueApi;
use Throwable;

class RunQueueCommand
{
    /** @var ExecutiveDi $di */
    private $di;
    /** @var QueueApi $queueApi */
    private $queueApi;

    public function __construct(ExecutiveDi $di, QueueApi $queueApi)
    {
        $this->di       = $di;
        $this->queueApi = $queueApi;
    }

    public function run() : ?int
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
    public function runInner(ActionQueueItemModel $item) : ?int
    {
        $constructedClass = null;

        if ($this->di->hasDefinition($item->class)) {
            $constructedClass = $this->di->makeFromDefinition($item->class);
        }

        if (! $constructedClass) {
            $constructedClass = new $item->class();
        }

        $constructedClass->{$item->method}($item->context);

        $this->queueApi->markItemAsRun($item);

        return null;
    }
}
