<?php
declare(strict_types=1);

namespace buzzingpixel\executive\services\queue;

use DateTime;
use DateTimeZone;
use buzzingpixel\executive\models\ActionQueueItemModel;
use buzzingpixel\executive\factories\QueryBuilderFactory;

class MarkQueueItemAsRunService
{
    private $queryBuilderFactory;
    private $updateActionQueueStatus;

    public function __construct(
        QueryBuilderFactory $queryBuilderFactory,
        UpdateActionQueueStatusService $updateActionQueueStatus
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->updateActionQueueStatus = $updateActionQueueStatus;
    }

    public function markQueueItemAsRun(ActionQueueItemModel $model): void
    {
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));

        $item = $this->queryBuilderFactory->make()
            ->where('id', $model->id)
            ->limit(1)
            ->get('executive_action_queue_items')
            ->row();

        if (! $item) {
            return;
        }

        $this->queryBuilderFactory->make()->update(
            'executive_action_queue_items',
            [
                'is_finished' => 1,
                'finished_at' => $dateTime->format('Y-m-d H:i:s'),
                'finished_at_time_zone' => $dateTime->getTimezone()->getName(),
            ],
            [
                'id' => $model->id,
            ]
        );

        $actionQueueQuery = $this->queryBuilderFactory->make()
            ->where('id', $item->action_queue_id)
            ->limit(1)
            ->get('executive_action_queue')
            ->row();

        if (! $actionQueueQuery) {
            return;
        }

        $this->updateActionQueueStatus->updateActionQueueStatus(
            $actionQueueQuery->id
        );
    }
}
