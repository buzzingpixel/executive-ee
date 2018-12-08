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

    public function __construct(QueryBuilderFactory $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    public function markQueueItemAsRun(ActionQueueItemModel $model): void
    {
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));

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
    }
}
