<?php
declare(strict_types=1);

namespace buzzingpixel\executive\services\queue;

use Throwable;
use buzzingpixel\executive\models\ActionQueueItemModel;
use buzzingpixel\executive\factories\QueryBuilderFactory;

class GetNextQueueItemService
{
    private $queryBuilderFactory;

    public function __construct(QueryBuilderFactory $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    public function getNextQueueItem(
        bool $markAsStarted = false
    ): ?ActionQueueItemModel {
        try {
            $actionQueueQuery = $this->queryBuilderFactory->make()
                ->where('is_finished', 0)
                ->order_by('added_at', 'ASC')
                ->limit(1)
                ->get('executive_action_queue')
                ->row();

            if (! $actionQueueQuery) {
                return null;
            }

            $itemQuery = $this->queryBuilderFactory->make()
                ->where('is_finished', 0)
                ->where('action_queue_id', $actionQueueQuery->id)
                ->order_by('order_to_run', 'ASC')
                ->limit(1)
                ->get('executive_action_queue_items')
                ->row();

            if (! $itemQuery) {
                $this->queryBuilderFactory->make()->update(
                    'executive_action_queue',
                    [
                        'has_started' => 1,
                        'is_finished' => 1,
                        'percent_complete' => 100,
                    ],
                    [
                        'id' => $actionQueueQuery->id,
                    ]
                );
                return null;
            }

            if ($markAsStarted && ! ((int) $actionQueueQuery->has_started)) {
                $this->queryBuilderFactory->make()->update(
                    'executive_action_queue',
                    [
                        'has_started' => 1,
                    ],
                    [
                        'id' => $actionQueueQuery->id,
                    ]
                );
            }

            $model = new ActionQueueItemModel();
            $model->id = $itemQuery->id;
            $model->isFinished = false;
            $model->class = $itemQuery->class;
            $model->method = $itemQuery->method;
            $model->context = json_decode($itemQuery->context, true) ?? [];

            return $model;
        } catch (Throwable $e) {
            return null;
        }
    }
}
