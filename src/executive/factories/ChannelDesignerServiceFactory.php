<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\services\ChannelDesignerService;
use DI\DependencyException;
use DI\NotFoundException;

class ChannelDesignerServiceFactory
{
    /**
     * Gets a ChannelDesignerService instance
     *
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public function make() : ChannelDesignerService
    {
        return ExecutiveDi::make(ChannelDesignerService::class);
    }
}
