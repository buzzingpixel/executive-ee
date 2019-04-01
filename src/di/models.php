<?php

declare(strict_types=1);

use buzzingpixel\executive\models\CliArgumentsModel;
use buzzingpixel\executive\models\RouteModel;
use function DI\autowire;

return [
    CliArgumentsModel::class => static function () {
        $arguments = defined('EXECUTIVE_RAW_ARGS')  ? EXECUTIVE_RAW_ARGS : [];
        $arguments = is_array($arguments) ? $arguments : [];

        return new CliArgumentsModel($arguments);
    },
    RouteModel::class => autowire(),
];
