# Queue

Executive has a queue to run tasks in the background. The queue is (or should be) run by the server and does not tie up PHP-FPM processes. And because it's a queue, you can break up your jobs into small chunks or tasks that are handled by the processes one at a time.

In order to take advantage of the queue, you must have something like Supervisor running the queue. There's a `queueRunner.sh` shell script in the `install-support` directory that you can have Supervisor run. That shell script uses a `while` loop to create an infinite loop to run the queue again after 1 second once the command completes. And if anything happens to it, Supervisor will start it right up again. Look in the script for more config and Supervisor examples.

## Adding items to the queue

Adding items to the queue is fairly simple. Here's an example:

```php
<?php
// Create an item model (an item is like a task), you can add as many of these
// as you want. The'll be run one at a time, one after the other, in the order
// you specify in the array below
$actionQueueItemModel1 = new \buzzingpixel\executive\models\ActionQueueItemModel();
$actionQueueItemModel1->class = SomeClass::class;
$actionQueueItemModel1->method = 'myMethod'; // If not provided will default to __invoke

// The context array is yours to put whatever you want in. Your method will
// receive this array as an argument when it's run
$actionQueueItemModel1->context = [
    'item' => 'thing',
    'stuff' => 'whatever',
];

// Just as an example, here's a second task we'll add to this batch
$actionQueueItemModel2 = new \buzzingpixel\executive\models\ActionQueueItemModel();
$actionQueueItemModel2->class = SomeClass::class;
$actionQueueItemModel2->context = [
    'asdf' => '12321',
    'stuff' => 'whatever',
];

// Now we'll create the action queue model
$actionQueueModel = new \buzzingpixel\executive\models\ActionQueueModel();

// Name and title are required.
$actionQueueModel->actionName = 'myTestAction';
$actionQueueModel->actionTitle = 'My Test Action';

// Add the models created above to the items array
$actionQueueModel->items = [
    $actionQueueItemModel1,
    $actionQueueItemModel2,
];

// Now grab the Queue API from the Executive Dependency Injector
// (hopefully, you'll dependency inject this into your class, but we're grabbing
// it directly here as an example)
/** @var \buzzingpixel\executive\services\QueueApi $queueApi */
$queueApi = \buzzingpixel\executive\ExecutiveDi::get(
    \buzzingpixel\executive\services\QueueApi::class
);

// Then call the addToQueue method and send it your model
$queueApi->addToQueue($actionQueueModel);
```
