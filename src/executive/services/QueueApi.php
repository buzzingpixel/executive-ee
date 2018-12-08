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
use buzzingpixel\executive\services\queue\AddToQueueService;
use buzzingpixel\executive\exceptions\InvalidActionQueueModel;

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
}
