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
use buzzingpixel\executive\services\queue\MarkQueueItemAsRunService;
use buzzingpixel\executive\services\queue\MarkAsStoppedDueToErrorService;
use buzzingpixel\executive\services\queue\UpdateActionQueueStatusService;

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

    public function markAsStoppedDueToError(ActionQueueItemModel $model): void
    {
        /** @var MarkAsStoppedDueToErrorService $service */
        $service = $this->di->getFromDefinition(MarkAsStoppedDueToErrorService::class);
        $service->markAsStoppedDueToError($model);
    }

    public function markItemAsRun(ActionQueueItemModel $model): void
    {
        /** @var MarkQueueItemAsRunService $service */
        $service = $this->di->getFromDefinition(MarkQueueItemAsRunService::class);
        $service->markQueueItemAsRun($model);
    }

    public function updateActionQueueStatus(int $actionQueueId): void
    {
        /** @var UpdateActionQueueStatusService $service */
        $service = $this->di->getFromDefinition(UpdateActionQueueStatusService::class);
        $service->updateActionQueueStatus($actionQueueId);
    }
}
