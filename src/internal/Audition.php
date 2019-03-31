<?php

declare(strict_types=1);

namespace buzzingpixel\executive\internal;

use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\models\RouteModel;
use buzzingpixel\executive\services\EETemplateService;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

class Audition
{
    public function route(RouteModel $router) : ResponseInterface
    {
        $response = new Response();

        $response = $response->withStatus(200)
            ->withHeader('Content-Type', 'text/html');

        $router->setPair('test_pair', [
            [
                'thing' => 'thingy',
                'test' => 'asdf',
            ],
            [
                'thing' => 'two-asdf',
                'test' => 'two',
            ],
        ]);

        /** @noinspection PhpUnhandledExceptionInspection */
        $response->getBody()->write(
            ExecutiveDi::diContainer()->get(EETemplateService::class)->renderTemplate(
                'test',
                'asdf',
                ['asdf' => 'thing']
            )
        );

        // $response->getBody()->write(
        //     ExecutiveDi::diContainer()->get(EETemplateService::class)->renderPath(
        //         'src/TestTemplate.html',
        //         [
        //             'asdf' => 'thing',
        //         ]
        //     )
        // );

        // $response->getBody()->write(
        //     ExecutiveDi::diContainer()->get(Environment::class)->render(
        //         'TestTwigTemplate.twig',
        //         [
        //             'testVar' => 'thingy',
        //         ]
        //     )
        // );

        return $response;
    }

    public function __invoke() : void
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
        // ExecutiveDi::diContainer()->get(QueueApi::class)->addToQueue($actionQueueModel);

        // // Get next queue item
        // var_dump(ExecutiveDi::diContainer()->get(QueueApi::class)->getNextQueueItem());
        // die;

        // // Get next queue item
        // ExecutiveDi::diContainer()->get(QueueApi::class)->markAsStoppedDueToError(
        //     ExecutiveDi::diContainer()->get(QueueApi::class)->getNextQueueItem()
        // );

        // // Mark item as run
        // ExecutiveDi::diContainer()->get(QueueApi::class)->markItemAsRun(
        //     ExecutiveDi::diContainer()->get(QueueApi::class)->getNextQueueItem()
        // );

        // // Update action queue entry status
        // ExecutiveDi::diContainer()->get(QueueApi::class)->updateActionQueueStatus(1);
    }
}
