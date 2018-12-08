<?php
declare(strict_types=1);

namespace buzzingpixel\executive\services\queue;

use DateTime;
use DateTimeZone;
use buzzingpixel\executive\factories\QueryBuilderFactory;

class UpdateActionQueueStatusService
{
    private $queryBuilderFactory;

    public function __construct(QueryBuilderFactory $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    public function updateActionQueueStatus(int $actionQueueId): void
    {
        $actionQueueQuery = $this->queryBuilderFactory->make()
            ->where('id', $actionQueueId)
            ->limit(1)
            ->get('executive_action_queue')
            ->row();

        if (! $actionQueueQuery) {
            return;
        }

        $items = $this->queryBuilderFactory->make()
            ->where('action_queue_id', $actionQueueId)
            ->get('executive_action_queue_items')
            ->result();

        $totalItems = \count($items);
        $totalRun = 0;

        foreach ($items as $item) {
            if (! ((int) $item->is_finished)) {
                continue;
            }

            $totalRun++;
        }

        if ($totalRun >= $totalItems && ((int) $actionQueueQuery->is_finished)) {
            return;
        }

        if ($totalRun >= $totalItems && ! ((int) $actionQueueQuery->is_finished)) {
            $dateTime = new DateTime();
            $dateTime->setTimezone(new DateTimeZone('UTC'));

            $this->queryBuilderFactory->make()->update(
                'executive_action_queue',
                [
                    'is_finished' => 1,
                    'finished_at' => $dateTime->format('Y-m-d H:i:s'),
                    'finished_at_time_zone' => $dateTime->getTimezone()->getName(),
                    'percent_complete' => 100,
                ],
                [
                    'id' => $actionQueueId,
                ]
            );

            return;
        }

        $percentComplete = ($totalRun / $totalItems) * 100;
        $percentComplete = $percentComplete > 100 ? 100 : $percentComplete;
        $percentComplete = $percentComplete < 0 ? 0 : $percentComplete;

        $this->queryBuilderFactory->make()->update(
            'executive_action_queue',
            [
                'percent_complete' => $percentComplete,
            ],
            [
                'id' => $actionQueueId,
            ]
        );
    }
}
