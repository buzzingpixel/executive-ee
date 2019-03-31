<?php

declare(strict_types=1);

use buzzingpixel\executive\models\CliArgumentsModel;
use buzzingpixel\executive\models\RouteModel;

return [
    CliArgumentsModel::class => static function () {
        $arguments = EXECUTIVE_RAW_ARGS;
        $arguments = is_array($arguments) ? $arguments : [];

        return new CliArgumentsModel($arguments);
    },
    RouteModel::class => static function () {
        return new RouteModel();
    },
];
