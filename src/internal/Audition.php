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

        $response->getBody()->write(
            ExecutiveDi::get(EETemplateService::class)->renderTemplate(
                'test',
                'asdf',
                ['asdf' => 'thing']
            )
        );

        // $response->getBody()->write(
        //     ExecutiveDi::get(EETemplateService::class)->renderPath(
        //         'src/TestTemplate.html',
        //         [
        //             'asdf' => 'thing',
        //         ]
        //     )
        // );

        // $response->getBody()->write(
        //     ExecutiveDi::get(Environment::class)->render(
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

        // // Update action queue entry status
        // ExecutiveDi::get(QueueApi::class)->updateActionQueueStatus(1);
    }
}
