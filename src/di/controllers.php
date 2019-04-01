<?php

declare(strict_types=1);

use buzzingpixel\executive\controllers\ConsoleController;
use buzzingpixel\executive\controllers\RunMigrationsController;
use buzzingpixel\executive\services\MigrationsService;
use Psr\Container\ContainerInterface;
use function DI\autowire;

return [
    ConsoleController::class => autowire(),
    RunMigrationsController::class => static function (ContainerInterface $di) {
        return new RunMigrationsController(
            '\buzzingpixel\executive\migrations',
            $di->get(MigrationsService::class)
        );
    },
];
