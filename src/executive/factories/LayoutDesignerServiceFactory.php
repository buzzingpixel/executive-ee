<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\LayoutDesignerService;
use DI\DependencyException;
use DI\NotFoundException;

class LayoutDesignerServiceFactory
{
    /**
     * Gets a ExtensionDesignerService instance
     *
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public function make() : LayoutDesignerService
    {
        return ExecutiveDi::make(LayoutDesignerService::class);
    }
}
