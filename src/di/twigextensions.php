<?php

declare(strict_types=1);

use buzzingpixel\executive\services\EETemplateService;
use buzzingpixel\executive\twigextensions\EETemplateTwigExtension;
use Psr\Container\ContainerInterface;

return [
    EETemplateTwigExtension::class => static function (ContainerInterface $di) {
        return new EETemplateTwigExtension(
            $di->get(EETemplateService::class)
        );
    },
];
