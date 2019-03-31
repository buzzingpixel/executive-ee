<?php

declare(strict_types=1);

use buzzingpixel\executive\controllers\ConsoleController;
use buzzingpixel\executive\controllers\RunMigrationsController;
use buzzingpixel\executive\models\CliArgumentsModel;
use buzzingpixel\executive\services\CommandsService;
use buzzingpixel\executive\services\MigrationsService;
use buzzingpixel\executive\services\RunCommandService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

return [
    ConsoleController::class => static function (ContainerInterface $di) {
        return new ConsoleController(
            $di->get(CliArgumentsModel::class),
            new ConsoleOutput(),
            $di->get(CommandsService::class),
            ee()->lang,
            $di->get(RunCommandService::class)
        );
    },
    RunMigrationsController::class => static function (ContainerInterface $di) {
        return new RunMigrationsController(
            '\buzzingpixel\executive\migrations',
            $di->get(MigrationsService::class)
        );
    },
];
