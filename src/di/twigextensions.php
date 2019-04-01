<?php

declare(strict_types=1);

use buzzingpixel\executive\twigextensions\EETemplateTwigExtension;
use function DI\autowire;

return [
    EETemplateTwigExtension::class => autowire(),
];
