<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services\queue;

use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\models\ActionQueueItemModel;
use DateTime;
use DateTimeZone;

class MarkAsStoppedDueToErrorService
{
    /** @var QueryBuilderFactory $queryBuilderFactory */
    private $queryBuilderFactory;

    public function __construct(QueryBuilderFactory $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    public function markAsStoppedDueToError(ActionQueueItemModel $model) : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));

        $actionQueueItemQuery = $this->queryBuilderFactory->make()
            ->where('id', $model->id)
            ->limit(1)
            ->get('executive_action_queue_items')
            ->row();

        $this->queryBuilderFactory->make()->update(
            'executive_action_queue',
            [
                'is_finished' => 1,
                'finished_due_to_error' => 1,
                'finished_at' => $dateTime->format('Y-m-d H:i:s'),
                'finished_at_time_zone' => $dateTime->getTimezone()->getName(),
            ],
            [
                'id' => $actionQueueItemQuery->action_queue_id,
            ]
        );
    }
}
