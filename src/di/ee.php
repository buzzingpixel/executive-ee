<?php

declare(strict_types=1);

return [
    EE_Config::class => static function () {
        return ee()->config;
    },
];
