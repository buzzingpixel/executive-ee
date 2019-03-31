<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\ExtensionDesignerService;
use DI\DependencyException;
use DI\NotFoundException;

class ExtensionDesignerServiceFactory
{
    /**
     * Gets a ExtensionDesignerService instance
     *
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public function make() : ExtensionDesignerService
    {
        return ExecutiveDi::make(ExtensionDesignerService::class);
    }
}
