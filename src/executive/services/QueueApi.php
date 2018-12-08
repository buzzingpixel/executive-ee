<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\models\ActionQueueModel;
use buzzingpixel\executive\models\ActionQueueItemModel;
use buzzingpixel\executive\services\queue\AddToQueueService;
use buzzingpixel\executive\exceptions\InvalidActionQueueModel;
use buzzingpixel\executive\services\queue\GetNextQueueItemService;

class QueueApi
{
    private $di;

    public function __construct(ExecutiveDi $di)
    {
        $this->di = $di;
    }

    /**
     * @throws InvalidActionQueueModel
     */
    public function addToQueue(ActionQueueModel $model): void
    {
        /** @var AddToQueueService $service */
        $service = $this->di->getFromDefinition(AddToQueueService::class);
        $service->addToQueue($model);
    }

    public function getNextQueueItem(
        bool $markAsStarted = false
    ): ?ActionQueueItemModel {
        /** @var GetNextQueueItemService $service */
        $service = $this->di->getFromDefinition(GetNextQueueItemService::class);
        return $service->getNextQueueItem($markAsStarted);
    }
}
