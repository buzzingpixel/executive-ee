<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\exceptions\InvalidActionQueueModel;
use buzzingpixel\executive\models\ActionQueueItemModel;
use buzzingpixel\executive\models\ActionQueueModel;
use buzzingpixel\executive\services\queue\AddToQueueService;
use buzzingpixel\executive\services\queue\GetNextQueueItemService;
use buzzingpixel\executive\services\queue\MarkAsStoppedDueToErrorService;
use buzzingpixel\executive\services\queue\MarkQueueItemAsRunService;
use buzzingpixel\executive\services\queue\UpdateActionQueueStatusService;
use Psr\Container\ContainerInterface;

class QueueApi
{
    /** @var ContainerInterface $di */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @throws InvalidActionQueueModel
     */
    public function addToQueue(ActionQueueModel $model) : void
    {
        $service = $this->di->get(AddToQueueService::class);
        $service->addToQueue($model);
    }

    public function getNextQueueItem(
        bool $markAsStarted = false
    ) : ?ActionQueueItemModel {
        $service = $this->di->get(GetNextQueueItemService::class);

        return $service->getNextQueueItem($markAsStarted);
    }

    public function markAsStoppedDueToError(ActionQueueItemModel $model) : void
    {
        $service = $this->di->get(MarkAsStoppedDueToErrorService::class);
        $service->markAsStoppedDueToError($model);
    }

    public function markItemAsRun(ActionQueueItemModel $model) : void
    {
        $service = $this->di->get(MarkQueueItemAsRunService::class);
        $service->markQueueItemAsRun($model);
    }

    public function updateActionQueueStatus(int $actionQueueId) : void
    {
        $service = $this->di->get(UpdateActionQueueStatusService::class);
        $service->updateActionQueueStatus($actionQueueId);
    }
}
