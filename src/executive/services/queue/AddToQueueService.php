<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services\queue;

use DateTime;
use DateTimeZone;
use buzzingpixel\executive\models\ActionQueueModel;
use buzzingpixel\executive\models\ActionQueueItemModel;
use buzzingpixel\executive\factories\QueryBuilderFactory;
use buzzingpixel\executive\exceptions\InvalidActionQueueModel;

class AddToQueueService
{
    private $queryBuilderFactory;

    public function __construct(QueryBuilderFactory $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * @throws InvalidActionQueueModel
     */
    public function addToQueue(ActionQueueModel $model): void
    {
        $this->validateModel($model);

        $model = $this->saveActionQueue($model);

        $this->saveActionQueueItems($model);
    }

    private function saveActionQueue(ActionQueueModel $model): ActionQueueModel
    {
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));

        $queryBuilder = $this->queryBuilderFactory->make();

        $queryBuilder->insert('executive_action_queue', [
            'action_name' => $model->actionName,
            'action_title' => $model->actionTitle,
            'has_started' => false,
            'is_finished' => false,
            'finished_due_to_error' => false,
            'percent_complete' => 0,
            'added_at' => $dateTime->format('Y-m-d H:i:s'),
            'added_at_time_zone' => $dateTime->getTimezone()->getName(),
            'finished_at' => null,
            'finished_at_time_zone' => null,
            'context' => json_encode($model->context),
        ]);

        $model->id = $queryBuilder->insert_id();

        return $model;
    }

    private function saveActionQueueItems(ActionQueueModel $model): void
    {
        $insertData = [];

        $order = 1;

        foreach ($model->items as $item) {
            $insertData[] = [
                'order_to_run' => $order,
                'action_queue_id' => $model->id,
                'is_finished' => false,
                'finished_at' => null,
                'finished_at_time_zone' => null,
                'class' => $item->class,
                'method' => $item->method,
                'context' => json_encode($item->context),
            ];

            $order++;
        }

        $this->queryBuilderFactory->make()->insert_batch(
            'executive_action_queue_items',
            $insertData
        );
    }

    /**
     * @throws InvalidActionQueueModel
     */
    private function validateModel(ActionQueueModel $model): void
    {
        if (! $model->actionName ||
            ! $model->actionTitle ||
            ! $model->items ||
            ! \is_array($model->items)
        ) {
            throw new InvalidActionQueueModel();
        }

        foreach ($model->items as $item) {
            if (! \is_object($item) ||
                \get_class($item) !== ActionQueueItemModel::class ||
                ! $item->class
            ) {
                throw new InvalidActionQueueModel();
            }

            $item->method = $item->method ?: '__invoke';

            if (! method_exists($item->class, $item->method)) {
                throw new InvalidActionQueueModel();
            }
        }
    }
}
