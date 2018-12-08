<?php
declare(strict_types=1);

namespace buzzingpixel\executive\internal;

use buzzingpixel\executive\Noop;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\QueueApi;
use buzzingpixel\executive\models\ActionQueueModel;
use buzzingpixel\executive\models\ActionQueueItemModel;

class Audition
{
    public function __invoke(): void
    {
        // // Test adding to queue
        // $actionQueueItemModel1 = new ActionQueueItemModel();
        // $actionQueueItemModel1->class = Noop::class;
        // $actionQueueItemModel1->context = [
        //     'item' => 'thing',
        //     'stuff' => 'whatever',
        // ];
        //
        // $actionQueueItemModel2 = new ActionQueueItemModel();
        // $actionQueueItemModel2->class = Noop::class;
        // $actionQueueItemModel2->method = 'noop';
        // $actionQueueItemModel2->context = [
        //     'asdf' => '12321',
        //     'stuff' => 'whatever',
        // ];
        //
        // $actionQueueModel = new ActionQueueModel();
        // $actionQueueModel->actionName = 'myTestAction';
        // $actionQueueModel->actionTitle = 'My Test Action';
        // $actionQueueModel->items = [
        //     $actionQueueItemModel1,
        //     $actionQueueItemModel2,
        // ];
        //
        // ExecutiveDi::get(QueueApi::class)->addToQueue($actionQueueModel);

        // // Get next queue item
        // var_dump(ExecutiveDi::get(QueueApi::class)->getNextQueueItem());
        // die;

        // // Get next queue item
        // ExecutiveDi::get(QueueApi::class)->markAsStoppedDueToError(
        //     ExecutiveDi::get(QueueApi::class)->getNextQueueItem()
        // );

        // // Mark item as run
        // ExecutiveDi::get(QueueApi::class)->markItemAsRun(
        //     ExecutiveDi::get(QueueApi::class)->getNextQueueItem()
        // );

        // Update action queue entry status
        ExecutiveDi::get(QueueApi::class)->updateActionQueueStatus(1);
    }
}
